@extends('layouts.admin')
@section('title', 'Catat Pasokan Baru')
@section('page-title', 'Catat Pasokan Baru')
@section('page-subtitle', 'Mencatat pasokan beras masuk dari petani — batch FIFO baru dibuat otomatis')

@section('content')
<div class="card" style="max-width: 720px;">
    <div class="card-header">
        <h3>📦 Form Pasokan Beras Masuk</h3>
        <a href="{{ route('admin.pasokan.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
    </div>
    <div class="card-body">
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:12px 16px; margin-bottom:20px; font-size:13px; color:#065f46;">
            ℹ️ <strong>Sistem FIFO Otomatis:</strong> Setiap pasokan yang dicatat akan otomatis membuat <strong>batch beras baru</strong> dengan tanggal masuk sebagai urutan antrean FIFO. Stok produk juga akan otomatis bertambah.
        </div>
        <form action="{{ route('admin.pasokan.store') }}" method="POST">
            @csrf
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Pemasok / Petani *</label>
                    <select name="id_pemasok" class="form-control" required>
                        <option value="">-- Pilih Pemasok --</option>
                        @foreach($pemasoks as $pm)
                        <option value="{{ $pm->id_pemasok }}" {{ old('id_pemasok') == $pm->id_pemasok ? 'selected' : '' }}>{{ $pm->nama_pemasok }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Produk Beras *</label>
                    <select name="id_produk" class="form-control" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produks as $pr)
                        <option value="{{ $pr->id_produk }}" {{ old('id_produk') == $pr->id_produk ? 'selected' : '' }}>{{ $pr->nama_produk }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Tanggal Pasokan Masuk Gudang *</label>
                    <input type="date" name="tgl_pasok" class="form-control" value="{{ old('tgl_pasok', now()->toDateString()) }}" required>
                </div>
                <div class="form-group">
                    <label>Tanggal Panen / Giling *
                        <small style="font-weight:400; color:#64748b;">(untuk akurasi FIFO)</small>
                    </label>
                    <input type="date" name="tgl_panen_giling" class="form-control" value="{{ old('tgl_panen_giling') }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tanggal Kadaluarsa *
                    <small style="font-weight:400; color:#64748b;">(estimasi umur simpan beras)</small>
                </label>
                <input type="date" name="tgl_kadaluarsa" class="form-control" value="{{ old('tgl_kadaluarsa') }}" required>
            </div>
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Jumlah (kg) *</label>
                    <input type="number" id="jumlah_kg" name="jumlah_kg" class="form-control" value="{{ old('jumlah_kg') }}" min="1" step="0.5" required placeholder="100">
                </div>
                <div class="form-group">
                    <label>Harga Beli / kg (Rp) *</label>
                    <input type="number" id="harga_per_kg" name="harga_per_kg" class="form-control" value="{{ old('harga_per_kg') }}" min="0" step="100" required placeholder="8000">
                </div>
            </div>
            <div class="form-group">
                <label>Total Bayar ke Petani</label>
                <input type="text" id="total_preview" class="form-control" style="background:#f8fafc; font-weight:700; color:#16a34a;" readonly placeholder="Otomatis dihitung">
            </div>
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn btn-primary">💾 Catat Pasokan & Buat Batch FIFO</button>
                <a href="{{ route('admin.pasokan.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function hitungTotal() {
        const qty = parseFloat(document.getElementById('jumlah_kg').value) || 0;
        const harga = parseFloat(document.getElementById('harga_per_kg').value) || 0;
        const total = qty * harga;
        document.getElementById('total_preview').value = total > 0 ? 'Rp ' + total.toLocaleString('id-ID') : '';
    }
    document.getElementById('jumlah_kg').addEventListener('input', hitungTotal);
    document.getElementById('harga_per_kg').addEventListener('input', hitungTotal);
</script>
@endsection
