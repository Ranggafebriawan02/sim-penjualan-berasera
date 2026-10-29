<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pengiriman;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\BatchBeras;
use App\Models\PengeluaranBatch;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    // Tampilan Keranjang Belanja
    public function index()
    {
        $cart = session()->get('cart', []);
        $totalHarga = 0;

        foreach ($cart as $item) {
            $totalHarga += $item['harga'] * $item['qty'];
        }

        return view('cart', compact('cart', 'totalHarga'));
    }

    // Tambah Produk ke Keranjang dari Katalog
    public function add(Request $request, $id)
    {
        $produk = Produk::with(['kategori', 'kualitas'])->findOrFail($id);
        $qty = max(1, (int)$request->input('qty', 1));

        if ($produk->stok < $qty) {
            return back()->with('error', 'Maaf, stok beras ' . $produk->nama_produk . ' tidak mencukupi (Tersisa: ' . $produk->stok . ' ' . $produk->satuan . ').');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
        } else {
            $cart[$id] = [
                'id_produk' => $produk->id_produk,
                'nama' => $produk->nama_produk,
                'kategori' => $produk->kategori->nama_kategori,
                'kualitas' => $produk->kualitas->nama_kualitas,
                'satuan' => $produk->satuan,
                'harga' => $produk->harga,
                'gambar' => $produk->gambar,
                'qty' => $qty,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Beras ' . $produk->nama_produk . ' berhasil dimasukkan ke keranjang!');
    }

    // Tambah Qty (+ button)
    public function increase($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $produk = Produk::find($id);
            if ($produk && $produk->stok < $cart[$id]['qty'] + 1) {
                return back()->with('error', 'Stok beras ' . $produk->nama_produk . ' tidak mencukupi (Tersisa: ' . $produk->stok . ').');
            }
            $cart[$id]['qty'] += 1;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    // Kurang Qty (- button)
    public function decrease($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($cart[$id]['qty'] > 1) {
                $cart[$id]['qty'] -= 1;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    // Update Manual (Fallback)
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $qty = (int)$request->input('qty', 1);

        if (isset($cart[$id])) {
            if ($qty > 0) {
                $cart[$id]['qty'] = $qty;
            } else {
                unset($cart[$id]);
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index');
    }

    // Hapus Produk dari Keranjang
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Beras telah dihapus dari keranjang.');
    }

    // PROSES CHECKOUT & ALOKASI STOK FIFO NYATA
    public function checkout(Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('login')->with('info', 'Silakan masuk akun terlebih dahulu untuk melanjutkan pembayaran.');
        }

        $customer = Auth::guard('customer')->user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong.');
        }

        $metode = $request->input('metode_transaksi', 'midtrans_qris');

        DB::beginTransaction();
        try {
            // 1. Validasi kecukupan stok seluruh item
            foreach ($cart as $id => $item) {
                $produk = Produk::lockForUpdate()->find($id);
                if (!$produk || $produk->stok < $item['qty']) {
                    DB::rollBack();
                    return back()->with('error', 'Maaf, stok beras ' . ($produk ? $produk->nama_produk : '') . ' sudah tidak mencukupi.');
                }
            }

            // 2. Hitung total belanja
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['harga'] * $item['qty'];
            }

            // Reward Poin: 1 Poin per Rp 10.000 belanja
            $poinDidapat = (int) floor($subtotal / 10000);

            // 3. Generate Nomor Faktur / ID Penjualan
            $idPenjualan = 'PJL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Default pengiriman internal Poktan Gapsera
            $idPengiriman = 1;

            // Buat record header penjualan (tanpa ongkos kirim)
            $penjualan = Penjualan::create([
                'id_penjualan' => $idPenjualan,
                'tgl_jual' => now()->toDateString(),
                'id_customer' => $customer->id_customer,
                'total' => $subtotal,
                'ongkir' => 0, // Tidak ada ongkos kirim sesuai instruksi
                'poin_digunakan' => 0,
                'diskon_poin' => 0,
                'poin_didapat' => $poinDidapat,
                'note' => 'Pesanan Beras Organik Berasera (DANA/QRIS)',
                'alamat_pengiriman' => $customer->alamat ?? 'Alamat Pelanggan',
                'status_penjualan' => 'menunggu_pembayaran',
                'metode_transaksi' => $metode,
                'id_pengiriman' => $idPengiriman,
                'id_user' => 1, // Default Ketua/Admin sistem
            ]);

            // 4. Proses Item Detail & Pemotongan Antrean FIFO
            foreach ($cart as $id => $item) {
                $subItem = $item['harga'] * $item['qty'];

                $detail = PenjualanDetail::create([
                    'id_penjualan' => $idPenjualan,
                    'id_produk' => $id,
                    'jumlah' => $item['qty'],
                    'harga_satuan' => $item['harga'],
                    'sub_total' => $subItem,
                    'status_detail' => 'aktif',
                ]);

                // Eksekusi Logika FIFO: Ambil dari Batch Masuk Beras Tertua
                $batches = BatchBeras::fifoQueue($id)->lockForUpdate()->get();
                $sisaKebutuhan = $item['qty'];

                foreach ($batches as $batch) {
                    if ($sisaKebutuhan <= 0) break;

                    $diambil = min($sisaKebutuhan, $batch->stok_sisa);

                    // Catat jejak mutasi alokasi FIFO
                    PengeluaranBatch::create([
                        'id_penjualan_detail' => $detail->id_penjualan_detail,
                        'id_batch' => $batch->id_batch,
                        'jumlah_keluar' => $diambil,
                        'hpp_satuan' => $batch->harga_pokok_beli,
                        'subtotal_hpp' => $diambil * $batch->harga_pokok_beli,
                    ]);

                    // Kurangi stok batch
                    $batch->stok_sisa -= $diambil;
                    if ($batch->stok_sisa <= 0) {
                        $batch->status_batch = 'habis';
                    }
                    $batch->save();

                    $sisaKebutuhan -= $diambil;
                }

                // Kurangi stok master produk
                $produk = Produk::find($id);
                $produk->stok = max(0, $produk->stok - $item['qty']);
                $produk->save();
            }

            // 5. Buat Record Pembayaran & Inisialisasi Midtrans Snap jika metode online
            $snapToken = null;
            if ($metode !== 'cash') {
                $midtransService = new \App\Services\MidtransService();
                // Eager load details and customer for Midtrans payload
                $penjualan->load(['details.produk', 'customer']);
                $snapToken = $midtransService->createSnapToken($penjualan);
            }

            Pembayaran::create([
                'id_penjualan' => $idPenjualan,
                'gateway_provider' => $metode === 'cash' ? 'cash' : 'midtrans',
                'metode_pembayaran' => $metode === 'cash' ? 'cash' : 'qris',
                'snap_token' => $snapToken,
                'nominal_bayar' => $subtotal,
                'status_pembayaran' => 'pending',
                'waktu_transaksi' => now(),
            ]);

            DB::commit();

            // Kosongkan keranjang belanja
            session()->forget('cart');

            return redirect()->route('payment.show', $idPenjualan)->with('success', 'Pesanan beras berhasil dibuat! Silakan lanjutkan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan: ' . $e->getMessage());
        }
    }

    // Halaman Pembayaran (Tampilan Midtrans Snap, QRIS, & Konfirmasi)
    public function paymentShow($id)
    {
        $penjualan = Penjualan::with([
            'customer', 
            'details.produk.kualitas', 
            'details.alokasiBatches.batch', 
            'pembayaran'
        ])->findOrFail($id);

        // Jika pembayaran online dan belum punya snap_token atau status masih pending, coba buat/perbarui
        if ($penjualan->metode_transaksi !== 'cash' && $penjualan->pembayaran && $penjualan->pembayaran->status_pembayaran === 'pending') {
            if (empty($penjualan->pembayaran->snap_token)) {
                $midtransService = new \App\Services\MidtransService();
                $snapToken = $midtransService->createSnapToken($penjualan);
                if ($snapToken) {
                    $penjualan->pembayaran->snap_token = $snapToken;
                    $penjualan->pembayaran->save();
                }
            }

            // Opsional: Cek status terkini ke Midtrans jika sudah ada transaksi
            $midtransService = new \App\Services\MidtransService();
            $midtransStatus = $midtransService->checkStatus($penjualan->id_penjualan);
            if ($midtransStatus && isset($midtransStatus->transaction_status)) {
                $statusTrx = $midtransStatus->transaction_status;
                if (in_array($statusTrx, ['settlement', 'capture'])) {
                    $penjualan->pembayaran->status_pembayaran = 'settlement';
                    $penjualan->pembayaran->waktu_bayar = now();
                    $penjualan->pembayaran->save();
                    $penjualan->refresh();
                } elseif (in_array($statusTrx, ['cancel', 'deny', 'expire'])) {
                    $penjualan->pembayaran->status_pembayaran = $statusTrx;
                    $penjualan->pembayaran->save();
                    $penjualan->refresh();
                }
            }
        }

        // Path barcode QRIS: periksa apakah ada barcode kustom yang diunggah
        $customQrPath = public_path('uploads/qris_custom.jpg');
        $qrisImage = file_exists($customQrPath) 
            ? asset('uploads/qris_custom.jpg') 
            : asset('images/qris_dana.jpg');

        $clientKey = config('midtrans.client_key');
        $isProduction = config('midtrans.is_production', false);

        return view('payment', compact('penjualan', 'qrisImage', 'clientKey', 'isProduction'));
    }

    // Konfirmasi Pembayaran Telah Dilakukan
    public function confirmPayment(Request $request, $id)
    {
        $penjualan = Penjualan::with('pembayaran')->findOrFail($id);
        $pembayaran = $penjualan->pembayaran;

        if (!$pembayaran) {
            return back()->with('error', 'Data pembayaran tidak ditemukan.');
        }

        // Jika ada unggahan bukti transfer/screenshot DANA
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = 'bukti_' . $penjualan->id_penjualan . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
        }

        // Update status pembayaran menjadi settlement (Lunas)
        // Ini otomatis memicu Database Trigger (trg_proses_settlement_pembayaran)
        $pembayaran->status_pembayaran = 'settlement';
        $pembayaran->waktu_bayar = now();
        $pembayaran->save();

        return redirect()->route('payment.show', $id)->with('success', 'Alhamdulillah! Pembayaran telah berhasil dikonfirmasi dan diverifikasi.');
    }

    // Upload Barcode QRIS DANA Sendiri
    public function uploadCustomQris(Request $request)
    {
        $request->validate([
            'foto_qris' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $file = $request->file('foto_qris');
        $file->move(public_path('uploads'), 'qris_custom.jpg');

        return back()->with('success', 'Barcode QRIS DANA Anda berhasil diperbarui! Barcode baru langsung tampil untuk pembeli.');
    }
}
