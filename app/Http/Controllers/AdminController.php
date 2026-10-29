<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BatchBeras;
use App\Models\Pasokan;
use App\Models\Penjualan;
use App\Models\Customer;
use App\Models\User;
use App\Models\Pemasok;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        $role = $user->level ?? 'bendahara';

        // ---- Metrik Ringkasan ----
        $totalProduk   = Produk::count();
        $totalStokKg   = Produk::sum('stok');
        $totalCustomer = Customer::count();
        $totalTransaksi = Penjualan::count();
        $totalOmset    = Penjualan::whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])->sum('total');
        $totalPemasok  = Pemasok::where('status_pemasok', 'aktif')->count();

        // ---- Peringatan Stok ----
        $stokMenipis  = Produk::where('stok', '<', 50)->where('stok', '>', 0)->with(['kategori'])->get();
        $stokHabis    = Produk::where('stok', '<=', 0)->count();

        // ---- Antrean FIFO Aktif ----
        $fifoBatches = BatchBeras::with(['produk.kategori', 'produk.kualitas'])
            ->where('stok_sisa', '>', 0)
            ->orderBy('tgl_masuk', 'asc')
            ->take(6)
            ->get();

        // ---- Pesanan Menunggu Konfirmasi ----
        $pesananMenunggu = Penjualan::with(['customer', 'pembayaran'])
            ->where('status_penjualan', 'menunggu_pembayaran')
            ->orderBy('tgl_jual', 'desc')
            ->take(5)
            ->get();

        // ---- 6 Transaksi Terbaru ----
        $transaksiTerbaru = Penjualan::with(['customer', 'pembayaran'])
            ->orderBy('tgl_jual', 'desc')
            ->take(6)
            ->get();

        // ---- 4 Pasokan Petani Terbaru ----
        $pasokanTerbaru = Pasokan::with(['pemasok', 'produk'])
            ->orderBy('tgl_pasok', 'desc')
            ->take(4)
            ->get();

        // ---- Penjualan 7 Hari Terakhir (Chart Data) ----
        $chartPenjualan = Penjualan::selectRaw('DATE(tgl_jual) as tanggal, COUNT(*) as jumlah_order, SUM(total) as total_omset')
            ->where('tgl_jual', '>=', now()->subDays(6)->toDateString())
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // ---- Ringkasan Keuangan Laba Kotor Bulan Berjalan ----
        $omsetBulanIni = Penjualan::whereYear('tgl_jual', now()->year)
            ->whereMonth('tgl_jual', now()->month)
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');

        $hppBulanIni = DB::table('tb_pengeluaran_batch as pb')
            ->join('tb_penjualan_detail as pd', 'pb.id_penjualan_detail', '=', 'pd.id_penjualan_detail')
            ->join('tb_penjualan as p', 'pd.id_penjualan', '=', 'p.id_penjualan')
            ->whereYear('p.tgl_jual', now()->year)
            ->whereMonth('p.tgl_jual', now()->month)
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->sum('pb.subtotal_hpp');

        $labaBulanIni = $omsetBulanIni - $hppBulanIni;

        // ---- Breakdown Komposisi Penjualan per Kategori (Donut Chart) ----
        $kategoriSales = DB::table('tb_penjualan_detail as pd')
            ->join('tb_penjualan as p', 'pd.id_penjualan', '=', 'p.id_penjualan')
            ->join('tb_produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
            ->join('tb_kategori as k', 'pr.id_kategori', '=', 'k.id_kategori')
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->select('k.nama_kategori', DB::raw('SUM(pd.jumlah) as total_kg'), DB::raw('SUM(pd.sub_total) as total_omset'))
            ->groupBy('k.nama_kategori')
            ->get();

        // ---- Laba Rugi FIFO ----
        try {
            $laporanFifo = DB::table('view_laporan_laba_rugi_fifo')->take(5)->get();
        } catch (\Exception $e) {
            $laporanFifo = collect();
        }

        return view('admin.dashboard', compact(
            'user', 'role',
            'totalProduk', 'totalStokKg', 'totalCustomer', 'totalTransaksi', 'totalOmset', 'totalPemasok',
            'stokMenipis', 'stokHabis',
            'fifoBatches', 'pesananMenunggu',
            'transaksiTerbaru', 'pasokanTerbaru',
            'chartPenjualan', 'laporanFifo',
            'omsetBulanIni', 'hppBulanIni', 'labaBulanIni', 'kategoriSales'
        ));
    }

    public function konfirmasiCash(Request $request, $idPenjualan)
    {
        $user = Auth::guard('web')->user();

        $penjualan  = Penjualan::findOrFail($idPenjualan);
        $pembayaran = Pembayaran::where('id_penjualan', $idPenjualan)->first();

        if ($pembayaran) {
            $pembayaran->update([
                'status_pembayaran' => 'settlement',
                'waktu_bayar'       => now(),
                'id_user'           => $user->id_user,
            ]);
        }

        $penjualan->update([
            'status_penjualan' => 'diverifikasi',
            'id_user'          => $user->id_user,
        ]);

        return back()->with('success', 'Pembayaran tunai pesanan #' . $idPenjualan . ' berhasil divalidasi!');
    }
}
