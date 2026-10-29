@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Beras')
@section('page-subtitle', 'Daftarkan produk beras baru ke katalog')

@section('content')
<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h3>🌾 Form Produk Baru</h3>
        <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="id_kategori" class="form-control" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k->id_kategori }}" {{ old('id_kategori') == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kualitas *</label>
                    <select name="id_kualitas" class="form-control" required>
                        <option value="">-- Pilih Kualitas --</option>
                        @foreach($kualitas as $kq)
                        <option value="{{ $kq->id_kualitas }}" {{ old('id_kualitas') == $kq->id_kualitas ? 'selected' : '' }}>{{ $kq->nama_kualitas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk') }}" required placeholder="cth: Beras Merah Organik Premium">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Deskripsi produk...">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label>Satuan *</label>
                    <select name="satuan" class="form-control" required>
                        <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="gram" {{ old('satuan') == 'gram' ? 'selected' : '' }}>gram</option>
                        <option value="karung" {{ old('satuan') == 'karung' ? 'selected' : '' }}>karung</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga / Satuan (Rp) *</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga') }}" min="0" step="100" required placeholder="15000">
                </div>
                <div class="form-group">
                    <label>Stok Awal *</label>
                    <input type="number" name="stok" class="form-control" value="{{ old('stok', 0) }}" min="0" required placeholder="100">
                </div>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <div style="font-size:11.5px; color:#94a3b8; margin-top:4px;">Format: JPG/PNG, maks 2MB</div>
            </div>
            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 Simpan Produk</button>
                <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
