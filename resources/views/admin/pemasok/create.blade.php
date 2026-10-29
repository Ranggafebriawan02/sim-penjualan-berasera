@extends('layouts.admin')
@section('title', 'Tambah Pemasok')
@section('page-title', 'Tambah Pemasok')
@section('page-subtitle', 'Daftarkan petani atau supplier beras baru')

@section('content')
<div class="card" style="max-width: 640px;">
    <div class="card-header">
        <h3>🚜 Form Pemasok Baru</h3>
        <a href="{{ route('admin.pemasok.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pemasok.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Pemasok / Petani *</label>
                <input type="text" name="nama_pemasok" class="form-control" value="{{ old('nama_pemasok') }}" required placeholder="cth: Pak Budi - Kelompok Tani Makmur">
            </div>
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" placeholder="0812-XXXX-XXXX">
                </div>
                <div class="form-group">
                    <label>Rekening Bank</label>
                    <input type="text" name="rekening_bank" class="form-control" value="{{ old('rekening_bank') }}" placeholder="BRI 123456789">
                </div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status_pemasok" class="form-control" required>
                    <option value="aktif" {{ old('status_pemasok') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status_pemasok') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="{{ route('admin.pemasok.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
