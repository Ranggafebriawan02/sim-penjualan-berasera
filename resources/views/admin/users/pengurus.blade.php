@extends('layouts.admin')
@section('title', 'Data Pengurus')
@section('page-title', 'Data Pengurus')
@section('page-subtitle', 'Kelola akun Ketua dan Bendahara Poktan Gapsera')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>👨‍💼 Daftar Pengurus</h3>
        <a href="{{ route('admin.users.pengurus.create') }}" class="btn btn-primary">+ Tambah Pengurus</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama</th><th>Username</th><th>Level</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td style="color:#94a3b8;">{{ $users->firstItem() + $i }}</td>
                    <td style="font-weight:700; font-size:13.5px;">{{ $u->nama }}</td>
                    <td style="font-size:13px; font-family:monospace; color:#64748b;">{{ $u->username }}</td>
                    <td>
                        <span class="badge {{ $u->level === 'ketua' ? 'badge-warning' : 'badge-info' }}">{{ ucfirst($u->level) }}</span>
                    </td>
                    <td>
                        <div style="display:flex; gap:6px;">
                            <a href="{{ route('admin.users.pengurus.edit', $u->id_user) }}" class="btn btn-sm btn-secondary">✏️ Edit</a>
                            <form action="{{ route('admin.users.pengurus.destroy', $u->id_user) }}" method="POST" onsubmit="return confirm('Hapus akun ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">🗑</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada pengurus.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding: 16px 22px;">{{ $users->links() }}</div>
    @endif
</div>
@endsection
