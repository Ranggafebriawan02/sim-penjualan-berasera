@extends('layouts.admin')
@section('title', 'Edit Pengurus')
@section('page-title', 'Edit Pengurus')
@section('page-subtitle', 'Edit data: ' . $user->nama)

@section('content')
<div class="card" style="max-width: 560px;">
    <div class="card-header">
        <h3>✏️ Edit Pengurus</h3>
        <a href="{{ route('admin.users.pengurus') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.pengurus.update', $user->id_user) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
            </div>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>
            <div class="form-group">
                <label>Level / Jabatan *</label>
                <select name="level" class="form-control" required>
                    <option value="ketua" {{ $user->level == 'ketua' ? 'selected' : '' }}>Ketua</option>
                    <option value="bendahara" {{ $user->level == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password Baru (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                <a href="{{ route('admin.users.pengurus') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
