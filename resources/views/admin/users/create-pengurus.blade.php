@extends('layouts.admin')
@section('title', 'Tambah Pengurus')
@section('page-title', 'Tambah Pengurus')
@section('page-subtitle', 'Buat akun baru untuk Ketua atau Bendahara')

@section('content')
<div class="card" style="max-width: 560px;">
    <div class="card-header">
        <h3>👨‍💼 Form Pengurus Baru</h3>
        <a href="{{ route('admin.users.pengurus') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.pengurus.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="cth: Ahmad Sugiono">
            </div>
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" class="form-control" value="{{ old('username') }}" required placeholder="cth: ketua2024">
            </div>
            <div class="form-group">
                <label>Level / Jabatan *</label>
                <select name="level" class="form-control" required>
                    <option value="ketua" {{ old('level') == 'ketua' ? 'selected' : '' }}>Ketua</option>
                    <option value="bendahara" {{ old('level') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                </select>
            </div>
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
            </div>
            <div class="form-group">
                <label>Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="{{ route('admin.users.pengurus') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
