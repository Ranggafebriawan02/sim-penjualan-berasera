@extends('layouts.admin')
@section('title', 'Edit Pemasok')
@section('page-title', 'Edit Pemasok')
@section('page-subtitle', 'Edit data: ' . $pemasok->nama_pemasok)

@section('content')
<div class="card" style="max-width: 640px;">
    <div class="card-header">
        <h3>✏️ Edit Pemasok</h3>
        <a href="{{ route('admin.pemasok.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pemasok.update', $pemasok->id_pemasok) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nama Pemasok / Petani *</label>
                <input type="text" name="nama_pemasok" class="form-control" value="{{ old('nama_pemasok', $pemasok->nama_pemasok) }}" required>
            </div>
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $pemasok->telepon) }}">
                </div>
                <div class="form-group">
                    <label>Rekening Bank</label>
                    <input type="text" name="rekening_bank" class="form-control" value="{{ old('rekening_bank', $pemasok->rekening_bank) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $pemasok->alamat) }}</textarea>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status_pemasok" class="form-control" required>
                    <option value="aktif" {{ $pemasok->status_pemasok == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ $pemasok->status_pemasok == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
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
