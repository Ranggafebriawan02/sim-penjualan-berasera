@extends('layouts.admin')
@section('title', 'Produk Beras')
@section('page-title', 'Produk Beras')
@section('page-subtitle', 'Kelola katalog produk beras organik')

@section('content')
@if($stokMenipis > 0)
<div class="stok-alert-bar">
    ⚠️ <strong>{{ $stokMenipis }} produk</strong> stok menipis (di bawah 50 kg). Segera lakukan pembelian pasokan!
    <a href="{{ route('admin.laporan.stok') }}" style="margin-left:auto; color:#92400e; font-weight:700;">Lihat laporan →</a>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>🌾 Daftar Produk Beras</h3>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Kualitas</th>
                    <th>Satuan</th>
                    <th>Harga/Satuan</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produk as $i => $p)
                <tr>
                    <td style="color:#94a3b8; font-size:12px;">{{ $produk->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:700; font-size:13.5px;">{{ $p->nama_produk }}</div>
                        @if($p->deskripsi)
                        <div style="font-size:11.5px; color:#64748b;">{{ Str::limit($p->deskripsi, 40) }}</div>
                        @endif
                    </td>
                    <td><span class="badge badge-info">{{ $p->kategori->nama_kategori ?? '—' }}</span></td>
                    <td style="font-size:12.5px;">{{ $p->kualitas->nama_kualitas ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $p->satuan }}</td>
                    <td style="font-weight:700; font-size:13px;">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td>
                        @if($p->stok <= 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($p->stok < 50)
                            <span class="badge badge-warning">{{ number_format($p->stok) }} kg ⚠️</span>
                        @else
                            <span class="badge badge-success">{{ number_format($p->stok) }} kg</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('admin.produk.edit', $p->id_produk) }}" class="btn btn-sm btn-secondary">✏️ Edit</a>
                            <form action="{{ route('admin.produk.destroy', $p->id_produk) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada produk. <a href="{{ route('admin.produk.create') }}">Tambah sekarang</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produk->hasPages())
    <div style="padding: 16px 22px;">{{ $produk->links() }}</div>
    @endif
</div>
@endsection
