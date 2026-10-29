@extends('layouts.admin')
@section('title', 'Pemasok / Petani')
@section('page-title', 'Pemasok / Petani')
@section('page-subtitle', 'Kelola data pemasok dan petani beras organik')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🚜 Daftar Pemasok</h3>
        <a href="{{ route('admin.pemasok.create') }}" class="btn btn-primary">+ Tambah Pemasok</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Pemasok</th><th>Telepon</th><th>Rekening Bank</th><th>Total Pasokan</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($pemasoks as $i => $p)
                <tr>
                    <td style="color:#94a3b8;">{{ $pemasoks->firstItem() + $i }}</td>
                    <td>
                        <div style="font-weight:700;">{{ $p->nama_pemasok }}</div>
                        @if($p->alamat)
                        <div style="font-size:11.5px; color:#64748b;">{{ Str::limit($p->alamat, 40) }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;">{{ $p->telepon ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $p->rekening_bank ?? '—' }}</td>
                    <td style="font-weight:600;">{{ number_format($p->pasokans_count) }}x</td>
                    <td>
                        <span class="badge {{ $p->status_pemasok === 'aktif' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($p->status_pemasok) }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('admin.pemasok.edit', $p->id_pemasok) }}" class="btn btn-sm btn-secondary">✏️ Edit</a>
                            <form action="{{ route('admin.pemasok.destroy', $p->id_pemasok) }}" method="POST" onsubmit="return confirm('Hapus pemasok ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada data pemasok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pemasoks->hasPages())
    <div style="padding: 16px 22px;">{{ $pemasoks->links() }}</div>
    @endif
</div>
@endsection
