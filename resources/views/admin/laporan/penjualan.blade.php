@extends('layouts.admin')
@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('page-subtitle', 'Rekap transaksi penjualan beras organik berdasarkan periode')

@section('content')
<!-- Filter Periode -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding: 16px 22px;">
        <form method="GET" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
            <div class="form-group" style="margin:0;">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}" class="form-control">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filter Laporan</button>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:20px;">
    <div class="card">
        <div class="card-body" style="text-align:center;">
            <div style="font-size:28px; font-weight:800; color:#16a34a;">{{ number_format($totalOrder) }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Total Pesanan Berhasil</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align:center;">
            <div style="font-size:22px; font-weight:800; color:#16a34a;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Total Omset</div>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="text-align:center;">
            <div style="font-size:22px; font-weight:800; color:#d97706;">Rp {{ number_format($rataOrder, 0, ',', '.') }}</div>
            <div style="font-size:13px; color:#64748b; margin-top:4px;">Rata-rata per Pesanan</div>
        </div>
    </div>
</div>

<!-- Per Produk -->
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h3>📊 Penjualan per Produk</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Produk</th><th>Total Terjual (kg)</th><th>Total Nilai</th><th>% Omset</th></tr>
            </thead>
            <tbody>
                @forelse($perProduk as $pp)
                <tr>
                    <td style="font-weight:700;">{{ $pp->nama_produk }}</td>
                    <td style="font-weight:700;">{{ number_format($pp->total_qty) }} kg</td>
                    <td style="font-weight:700; color:#16a34a;">Rp {{ number_format($pp->total_nilai, 0, ',', '.') }}</td>
                    <td>
                        @php $pct = $totalOmset > 0 ? ($pp->total_nilai / $totalOmset * 100) : 0; @endphp
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div style="flex:1; background:#e8f5ee; border-radius:4px; height:8px;">
                                <div style="width:{{ min(100, $pct) }}%; background:#16a34a; height:8px; border-radius:4px;"></div>
                            </div>
                            <span style="font-size:12px; font-weight:600;">{{ number_format($pct, 1) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:24px;">Tidak ada data penjualan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Transaksi -->
<div class="card">
    <div class="card-header"><h3>📋 Detail Transaksi ({{ $dari }} s/d {{ $sampai }})</h3></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>No. Faktur</th><th>Tanggal</th><th>Pembeli</th><th>Total</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($penjualans as $pj)
                <tr>
                    <td><a href="{{ route('admin.penjualan.show', $pj->id_penjualan) }}" style="color:#16a34a; font-weight:700; font-size:12.5px;">{{ $pj->id_penjualan }}</a></td>
                    <td style="font-size:12.5px;">{{ \Carbon\Carbon::parse($pj->tgl_jual)->format('d M Y') }}</td>
                    <td style="font-size:13px;">{{ $pj->customer->nama_customer ?? '—' }}</td>
                    <td style="font-weight:700;">Rp {{ number_format($pj->total, 0, ',', '.') }}</td>
                    <td><span class="badge badge-success">{{ ucfirst($pj->status_penjualan) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:24px;">Tidak ada transaksi dalam periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
