@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk Beras')
@section('page-subtitle', 'Perbarui data produk: ' . $produk->nama_produk)

@section('content')
<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h3>✏️ Edit Produk</h3>
        <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.produk.update', $produk->id_produk) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Kategori *</label>
                    <select name="id_kategori" class="form-control" required>
                        @foreach($kategoris as $k)
                        <option value="{{ $k->id_kategori }}" {{ $produk->id_kategori == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Kualitas *</label>
                    <select name="id_kualitas" class="form-control" required>
                        @foreach($kualitas as $kq)
                        <option value="{{ $kq->id_kualitas }}" {{ $produk->id_kualitas == $kq->id_kualitas ? 'selected' : '' }}>{{ $kq->nama_kualitas }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Nama Produk *</label>
                <input type="text" name="nama_produk" class="form-control" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label>Satuan *</label>
                    <select name="satuan" class="form-control" required>
                        <option value="kg" {{ $produk->satuan == 'kg' ? 'selected' : '' }}>kg</option>
                        <option value="gram" {{ $produk->satuan == 'gram' ? 'selected' : '' }}>gram</option>
                        <option value="karung" {{ $produk->satuan == 'karung' ? 'selected' : '' }}>karung</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga / Satuan (Rp) *</label>
                    <input type="number" name="harga" class="form-control" value="{{ old('harga', $produk->harga) }}" min="0" step="100" required>
                </div>
                <div class="form-group">
                    <label>Stok *</label>
                    <input type="number" name="stok" class="form-control" value="{{ old('stok', $produk->stok) }}" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Ganti Gambar (opsional)</label>
                @if($produk->gambar)
                    <div style="margin-bottom:8px;"><img src="{{ asset('images/'.$produk->gambar) }}" style="height:60px; border-radius:8px; border:1px solid #e2e8f0;" onerror="this.style.display='none'"></div>
                @endif
                <input type="file" name="gambar" class="form-control" accept="image/*">
            </div>
            <div style="display:flex; gap:10px; margin-top:8px;">
                <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
