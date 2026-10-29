@extends('layouts.admin')
@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok Beras')
@section('page-subtitle', 'Status persediaan beras & antrean FIFO batch aktif')

@section('content')

<!-- Summary -->
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;">
    <div class="card"><div class="card-body" style="text-align:center;">
        <div style="font-size:26px; font-weight:800; color:#16a34a;">{{ number_format($totalStok) }}</div>
        <div style="font-size:12px; color:#64748b;">Total Stok (kg)</div>
    </div></div>
    <div class="card"><div class="card-body" style="text-align:center;">
        <div style="font-size:20px; font-weight:800; color:#16a34a;">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</div>
        <div style="font-size:12px; color:#64748b;">Nilai Stok</div>
    </div></div>
    <div class="card"><div class="card-body" style="text-align:center;">
        <div style="font-size:26px; font-weight:800; color:#f59e0b;">{{ $stokMenipis->count() }}</div>
        <div style="font-size:12px; color:#64748b;">Stok Menipis (&lt;50kg)</div>
    </div></div>
    <div class="card"><div class="card-body" style="text-align:center;">
        <div style="font-size:26px; font-weight:800; color:#ef4444;">{{ $stokHabis->count() }}</div>
        <div style="font-size:12px; color:#64748b;">Stok Habis</div>
    </div></div>
</div>

@if($stokMenipis->count() > 0)
<div style="background:#fffbeb; border:1px solid #fde68a; border-radius:12px; padding:16px 20px; margin-bottom:20px;">
    <div style="font-weight:700; color:#92400e; margin-bottom:10px;">⚠️ Produk Stok Menipis — Segera Lakukan Pembelian!</div>
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:10px;">
        @foreach($stokMenipis as $p)
        <div style="background:white; border:1px solid #fde68a; border-radius:10px; padding:12px 14px; display:flex; align-items:center; gap:10px;">
            <span style="font-size:20px;">🌾</span>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:700;">{{ Str::limit($p->nama_produk, 22) }}</div>
                <div style="font-size:12px; color:#64748b;">{{ $p->kategori->nama_kategori ?? '' }}</div>
            </div>
            <span style="font-weight:800; color:#d97706; font-size:13px;">{{ number_format($p->stok) }} kg</span>
        </div>
        @endforeach
    </div>
    <div style="margin-top:12px;">
        <a href="{{ route('admin.pasokan.create') }}" class="btn btn-warning">📦 Catat Pasokan Baru</a>
    </div>
</div>
@endif

<!-- Tabel Semua Produk + FIFO Batches -->
<div class="card">
    <div class="card-header">
        <h3>📋 Status Stok Semua Produk</h3>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Produk</th><th>Kategori</th><th>Kualitas</th><th>Harga/kg</th><th>Stok</th><th>Nilai Stok</th><th>Batch Aktif</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @foreach($produks as $p)
                @php $batchAktif = $p->batches->where('stok_sisa', '>', 0)->count(); @endphp
                <tr>
                    <td style="font-weight:700; font-size:13.5px;">{{ $p->nama_produk }}</td>
                    <td><span class="badge badge-info">{{ $p->kategori->nama_kategori ?? '—' }}</span></td>
                    <td style="font-size:12.5px;">{{ $p->kualitas->nama_kualitas ?? '—' }}</td>
                    <td style="font-size:13px;">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td>
                        @if($p->stok <= 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($p->stok < 50)
                            <span class="badge badge-warning">{{ number_format($p->stok) }} kg ⚠️</span>
                        @else
                            <span class="badge badge-success">{{ number_format($p->stok) }} kg</span>
                        @endif
                    </td>
                    <td style="font-size:12.5px;">Rp {{ number_format($p->stok * $p->harga, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $batchAktif > 0 ? 'badge-info' : 'badge-danger' }}">
                            {{ $batchAktif }} batch
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.produk.edit', $p->id_produk) }}" class="btn btn-sm btn-secondary">✏️ Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
