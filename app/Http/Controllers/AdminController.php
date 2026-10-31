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

        // ================================================================
        // ---- ANALISIS KECERDASAN PENJUALAN (AI-STYLE INSIGHTS) ----
        // ================================================================

        // [1] Perbandingan Omset: Bulan Ini vs Bulan Lalu
        $omsetBulanLalu = Penjualan::whereYear('tgl_jual', now()->subMonth()->year)
            ->whereMonth('tgl_jual', now()->subMonth()->month)
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->sum('total');
        $pertumbuhanOmset = $omsetBulanLalu > 0
            ? round((($omsetBulanIni - $omsetBulanLalu) / $omsetBulanLalu) * 100, 1)
            : ($omsetBulanIni > 0 ? 100 : 0);

        // [2] Produk Terlaris (Top 5 berdasarkan volume kg & omset)
        $produkTerlaris = DB::table('tb_penjualan_detail as pd')
            ->join('tb_penjualan as p', 'pd.id_penjualan', '=', 'p.id_penjualan')
            ->join('tb_produk as pr', 'pd.id_produk', '=', 'pr.id_produk')
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->whereYear('p.tgl_jual', now()->year)
            ->whereMonth('p.tgl_jual', now()->month)
            ->select(
                'pr.nama_produk',
                DB::raw('SUM(pd.jumlah) as total_kg'),
                DB::raw('SUM(pd.sub_total) as total_omset'),
                DB::raw('COUNT(DISTINCT pd.id_penjualan) as jumlah_transaksi')
            )
            ->groupBy('pr.id_produk', 'pr.nama_produk')
            ->orderByDesc('total_kg')
            ->limit(5)
            ->get();

        // [3] Pelanggan Paling Loyal (Top 5 berdasarkan total belanja)
        $pelangganLoyal = DB::table('tb_penjualan as p')
            ->join('tb_customer as c', 'p.id_customer', '=', 'c.id_customer')
            ->whereIn('p.status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->select(
                'c.nama_customer',
                'c.tier_membership',
                'c.total_poin',
                DB::raw('COUNT(p.id_penjualan) as jumlah_order'),
                DB::raw('SUM(p.total) as total_belanja')
            )
            ->groupBy('p.id_customer', 'c.nama_customer', 'c.tier_membership', 'c.total_poin')
            ->orderByDesc('total_belanja')
            ->limit(5)
            ->get();

        // [4] Tren Omset 6 Bulan Terakhir (untuk chart tren bulanan)
        $trenBulanan = Penjualan::selectRaw('YEAR(tgl_jual) as tahun, MONTH(tgl_jual) as bulan, SUM(total) as total_omset, COUNT(*) as jumlah_order')
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->where('tgl_jual', '>=', now()->subMonths(5)->startOfMonth())
            ->groupByRaw('YEAR(tgl_jual), MONTH(tgl_jual)')
            ->orderByRaw('YEAR(tgl_jual), MONTH(tgl_jual)')
            ->get()
            ->map(function ($row) {
                $row->label = \Carbon\Carbon::create($row->tahun, $row->bulan, 1)->translatedFormat('M Y');
                return $row;
            });

        // [5] Analisis Metode Pembayaran
        $analisisMetode = Penjualan::selectRaw('metode_transaksi, COUNT(*) as jumlah, SUM(total) as total_omset')
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->groupBy('metode_transaksi')
            ->get();

        // [6] Rata-rata Nilai Transaksi (Average Order Value)
        $totalTrxBulanIni = Penjualan::whereYear('tgl_jual', now()->year)
            ->whereMonth('tgl_jual', now()->month)
            ->whereIn('status_penjualan', ['diverifikasi', 'diproses', 'dikirim', 'selesai'])
            ->count();
        $aovBulanIni = $totalTrxBulanIni > 0 ? $omsetBulanIni / $totalTrxBulanIni : 0;

        // [7] Insight Otomatis — Rekomendasi berbasis data
        $insightList = [];

        // Insight: pertumbuhan omset
        if ($pertumbuhanOmset >= 10) {
            $insightList[] = ['type' => 'success', 'icon' => '🚀', 'msg' => "Omset bulan ini tumbuh <strong>{$pertumbuhanOmset}%</strong> dibanding bulan lalu. Pertahankan tren positif ini!"];
        } elseif ($pertumbuhanOmset < 0) {
            $insightList[] = ['type' => 'danger', 'icon' => '📉', 'msg' => "Omset bulan ini turun <strong>" . abs($pertumbuhanOmset) . "%</strong> dari bulan lalu. Pertimbangkan promo atau diskon."];
        } else {
            $insightList[] = ['type' => 'warning', 'icon' => '📊', 'msg' => "Pertumbuhan omset bulan ini <strong>{$pertumbuhanOmset}%</strong>. Ada peluang meningkatkan penjualan lebih jauh."];
        }

        // Insight: stok kritis
        if ($stokHabis > 0) {
            $insightList[] = ['type' => 'danger', 'icon' => '⚠️', 'msg' => "<strong>{$stokHabis} varian beras</strong> stoknya habis. Segera koordinasi pengadaan dengan petani pemasok."];
        }
        if ($stokMenipis->count() > 0) {
            $insightList[] = ['type' => 'warning', 'icon' => '📦', 'msg' => "<strong>{$stokMenipis->count()} varian beras</strong> stok di bawah 50 Kg. Rencanakan restock dalam waktu dekat."];
        }

        // Insight: produk terlaris bulan ini
        if ($produkTerlaris->count() > 0) {
            $topProduk = $produkTerlaris->first();
            $insightList[] = ['type' => 'info', 'icon' => '🌾', 'msg' => "Produk terlaris bulan ini: <strong>{$topProduk->nama_produk}</strong> ({$topProduk->total_kg} Kg terjual). Pastikan stoknya tercukupi."];
        }

        // Insight: AOV
        if ($aovBulanIni > 0) {
            $aovFormatted = 'Rp ' . number_format($aovBulanIni, 0, ',', '.');
            $insightList[] = ['type' => 'info', 'icon' => '🧾', 'msg' => "Rata-rata nilai transaksi (AOV) bulan ini <strong>{$aovFormatted}</strong>. Tingkatkan dengan bundling produk."];
        }

        return view('admin.dashboard', compact(
            'user', 'role',
            'totalProduk', 'totalStokKg', 'totalCustomer', 'totalTransaksi', 'totalOmset', 'totalPemasok',
            'stokMenipis', 'stokHabis',
            'fifoBatches', 'pesananMenunggu',
            'transaksiTerbaru', 'pasokanTerbaru',
            'chartPenjualan', 'laporanFifo',
            'omsetBulanIni', 'hppBulanIni', 'labaBulanIni', 'kategoriSales',
            // Data Analitik Cerdas
            'omsetBulanLalu', 'pertumbuhanOmset',
            'produkTerlaris', 'pelangganLoyal',
            'trenBulanan', 'analisisMetode',
            'aovBulanIni', 'insightList'
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

        return back()
            ->with('success', 'Pembayaran tunai pesanan #' . $idPenjualan . ' berhasil divalidasi!')
            ->with('audio_payment_success', [
                'nominal' => $penjualan->total,
                'aktor'   => 'admin',
            ]);
    }
}
