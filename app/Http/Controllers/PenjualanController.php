<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pembayaran;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penjualan::with(['customer', 'pembayaran'])
            ->orderBy('tgl_jual', 'desc');

        if ($request->filled('status')) {
            $query->where('status_penjualan', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_penjualan', 'like', "%$search%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('nama_customer', 'like', "%$search%"));
            });
        }

        $penjualans = $query->paginate(20);
        return view('admin.penjualan.index', compact('penjualans'));
    }

    public function show($id)
    {
        $penjualan = Penjualan::with([
            'customer',
            'details.produk',
            'pembayaran',
            'user'
        ])->findOrFail($id);

        return view('admin.penjualan.show', compact('penjualan'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_penjualan' => 'required|in:menunggu_pembayaran,diverifikasi,diproses,dikirim,selesai,dibatalkan',
        ]);

        $penjualan = Penjualan::findOrFail($id);
        $penjualan->update([
            'status_penjualan' => $request->status_penjualan,
            'id_user'          => Auth::guard('web')->id(),
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi: ' . $request->status_penjualan);
    }

    public function konfirmasiPembayaran(Request $request, $id)
    {
        $penjualan  = Penjualan::with('pembayaran')->findOrFail($id);
        $pembayaran = $penjualan->pembayaran;

        if ($pembayaran) {
            $pembayaran->update([
                'status_pembayaran' => 'settlement',
                'waktu_bayar'       => now(),
                'id_user'           => Auth::guard('web')->id(),
            ]);
        }

        $penjualan->update([
            'status_penjualan' => 'diverifikasi',
            'id_user'          => Auth::guard('web')->id(),
        ]);

        return back()->with('success', 'Pembayaran pesanan #' . $id . ' berhasil dikonfirmasi!');
    }

    public function tolakPembayaran(Request $request, $id)
    {
        $penjualan  = Penjualan::with('pembayaran')->findOrFail($id);
        $pembayaran = $penjualan->pembayaran;

        if ($pembayaran) {
            $pembayaran->update(['status_pembayaran' => 'cancel']);
        }

        $penjualan->update([
            'status_penjualan' => 'dibatalkan',
            'id_user'          => Auth::guard('web')->id(),
        ]);

        return back()->with('success', 'Pesanan #' . $id . ' telah ditolak/dibatalkan.');
    }
}
