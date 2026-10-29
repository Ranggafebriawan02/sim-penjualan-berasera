<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\BatchBeras;
use App\Models\Pasokan;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index');
    }

    public function penjualan(Request $request)
    {
        $dari  = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $penjualans = Penjualan::with(['customer', 'pembayaran', 'details.produk'])
            ->whereBetween('tgl_jual', [$dari, $sampai])
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->orderBy('tgl_jual', 'desc')
            ->get();

        $totalOmset  = $penjualans->sum('total');
        $totalOrder  = $penjualans->count();
        $rataOrder   = $totalOrder > 0 ? $totalOmset / $totalOrder : 0;

        // Penjualan per produk
        $perProduk = DB::table('tb_penjualan_detail as d')
            ->join('tb_penjualan as p', 'd.id_penjualan', '=', 'p.id_penjualan')
            ->join('tb_produk as pr', 'd.id_produk', '=', 'pr.id_produk')
            ->whereBetween('p.tgl_jual', [$dari, $sampai])
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->select('pr.nama_produk', DB::raw('SUM(d.jumlah) as total_qty'), DB::raw('SUM(d.sub_total) as total_nilai'))
            ->groupBy('pr.id_produk', 'pr.nama_produk')
            ->orderByDesc('total_nilai')
            ->get();

        return view('admin.laporan.penjualan', compact('penjualans', 'totalOmset', 'totalOrder', 'rataOrder', 'perProduk', 'dari', 'sampai'));
    }

    public function stok(Request $request)
    {
        $produks = Produk::with(['kategori', 'kualitas', 'batches' => function ($q) {
            $q->where('stok_sisa', '>', 0)->orderBy('tgl_masuk');
        }])->orderBy('nama_produk')->get();

        $stokMenipis   = $produks->where('stok', '<', 50)->where('stok', '>', 0);
        $stokHabis     = $produks->where('stok', '<=', 0);
        $totalStok     = $produks->sum('stok');
        $totalNilaiStok = $produks->sum(fn($p) => $p->stok * $p->harga);

        return view('admin.laporan.stok', compact('produks', 'stokMenipis', 'stokHabis', 'totalStok', 'totalNilaiStok'));
    }

    public function pembelian(Request $request)
    {
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $pasokans = Pasokan::with(['pemasok', 'produk', 'batch', 'user'])
            ->whereBetween('tgl_pasok', [$dari, $sampai])
            ->orderBy('tgl_pasok', 'desc')
            ->get();

        $totalPembelian  = $pasokans->sum('total_bayar');
        $totalVolume     = $pasokans->sum('jumlah_kg');

        return view('admin.laporan.pembelian', compact('pasokans', 'totalPembelian', 'totalVolume', 'dari', 'sampai'));
    }

    public function labaRugi(Request $request)
    {
        $dari   = $request->input('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        try {
            $fifoData = DB::table('view_laporan_laba_rugi_fifo')
                ->whereBetween('tgl_jual', [$dari, $sampai])
                ->orderBy('tgl_jual', 'desc')
                ->get();
        } catch (\Exception $e) {
            $fifoData = collect();
        }

        // Hitung total laba dari periode
        $totalPenjualan = Penjualan::whereBetween('tgl_jual', [$dari, $sampai])
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');

        $totalHPP = DB::table('tb_pengeluaran_batch as pb')
            ->join('tb_penjualan_detail as pd', 'pb.id_penjualan_detail', '=', 'pd.id_penjualan_detail')
            ->join('tb_penjualan as p', 'pd.id_penjualan', '=', 'p.id_penjualan')
            ->whereBetween('p.tgl_jual', [$dari, $sampai])
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->sum('pb.subtotal_hpp');

        $labaKotor = $totalPenjualan - $totalHPP;
        $marginPersen = $totalPenjualan > 0 ? ($labaKotor / $totalPenjualan) * 100 : 0;

        return view('admin.laporan.laba-rugi', compact('fifoData', 'totalPenjualan', 'totalHPP', 'labaKotor', 'marginPersen', 'dari', 'sampai'));
    }
}
