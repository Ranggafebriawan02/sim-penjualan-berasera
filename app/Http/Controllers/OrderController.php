<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Riwayat Pesanan Pembeli
    public function riwayat()
    {
        $customer  = Auth::guard('customer')->user();
        $penjualans = Penjualan::with(['details.produk', 'pembayaran'])
            ->where('id_customer', $customer->id_customer)
            ->orderBy('tgl_jual', 'desc')
            ->paginate(10);

        return view('orders.riwayat', compact('penjualans'));
    }

    // Detail Pesanan & Cetak Struk
    public function struk($id)
    {
        $customer = Auth::guard('customer')->user();
        $penjualan = Penjualan::with([
            'customer',
            'details.produk.kategori',
            'details.produk.kualitas',
            'pembayaran'
        ])->where('id_customer', $customer->id_customer)
          ->findOrFail($id);

        return view('orders.struk', compact('penjualan'));
    }

    // Simulasi status pembayaran (menunggu → berhasil / gagal)
    public function simulasiPembayaran(Request $request, $id)
    {
        $request->validate([
            'aksi' => 'required|in:berhasil,gagal',
        ]);

        $customer  = Auth::guard('customer')->user();
        $penjualan = Penjualan::with('pembayaran')
            ->where('id_customer', $customer->id_customer)
            ->findOrFail($id);

        $pembayaran = $penjualan->pembayaran;

        if ($request->aksi === 'berhasil') {
            if ($pembayaran) {
                $pembayaran->update([
                    'status_pembayaran' => 'settlement',
                    'waktu_bayar'       => now(),
                ]);
            }
            $penjualan->update(['status_penjualan' => 'diverifikasi']);
            return back()->with('success', '✅ Pembayaran berhasil dikonfirmasi! Pesanan Anda sedang diproses.');
        } else {
            if ($pembayaran) {
                $pembayaran->update(['status_pembayaran' => 'cancel']);
            }
            $penjualan->update(['status_penjualan' => 'dibatalkan']);
            return back()->with('error', '❌ Pembayaran gagal/dibatalkan. Pesanan telah dibatalkan.');
        }
    }
}
