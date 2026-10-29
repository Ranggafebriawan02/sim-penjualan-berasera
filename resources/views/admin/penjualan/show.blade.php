@extends('layouts.admin')
@section('title', 'Detail Transaksi #' . $penjualan->id_penjualan)
@section('page-title', 'Detail Pesanan')
@section('page-subtitle', 'No. Faktur: ' . $penjualan->id_penjualan)

@section('content')
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Left: Detail Pesanan -->
    <div>
        <div class="card" style="margin-bottom: 20px;">
            <div class="card-header">
                <h3>📋 Item Pesanan</h3>
                <span style="font-size:13px; color:#64748b;">{{ \Carbon\Carbon::parse($penjualan->tgl_jual)->format('d M Y') }}</span>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->details as $det)
                        <tr>
                            <td>
                                <div style="font-weight:700; font-size:13.5px;">{{ $det->produk->nama_produk ?? '—' }}</div>
                            </td>
                            <td style="font-size:13px;">Rp {{ number_format($det->harga_satuan, 0, ',', '.') }}</td>
                            <td style="font-weight:700;">{{ $det->jumlah }} kg</td>
                            <td style="font-weight:700; color:#16a34a;">Rp {{ number_format($det->sub_total, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background:#f0fdf4;">
                            <td colspan="3" style="font-weight:700; text-align:right; padding:12px 14px;">TOTAL</td>
                            <td style="font-weight:800; font-size:15px; color:#16a34a; padding:12px 14px;">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Update Status -->
        <div class="card">
            <div class="card-header"><h3>🔄 Update Status Pesanan</h3></div>
            <div class="card-body">
                <form action="{{ route('admin.penjualan.status', $penjualan->id_penjualan) }}" method="POST" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                    @csrf
                    <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                        <label>Status Baru</label>
                        <select name="status_penjualan" class="form-control">
                            <option value="menunggu_pembayaran" {{ $penjualan->status_penjualan == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="diverifikasi" {{ $penjualan->status_penjualan == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                            <option value="diproses" {{ $penjualan->status_penjualan == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="dikirim" {{ $penjualan->status_penjualan == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="selesai" {{ $penjualan->status_penjualan == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ $penjualan->status_penjualan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">💾 Update</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right: Info Pembeli & Pembayaran -->
    <div>
        <div class="card" style="margin-bottom: 16px;">
            <div class="card-header"><h3>👤 Info Pembeli</h3></div>
            <div class="card-body">
                <div style="display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                    <div><span style="color:#64748b;">Nama:</span><br><strong>{{ $penjualan->customer->nama_customer ?? '—' }}</strong></div>
                    <div><span style="color:#64748b;">Telepon:</span><br>{{ $penjualan->customer->telepon ?? '—' }}</div>
                    <div><span style="color:#64748b;">Email:</span><br>{{ $penjualan->customer->email ?? '—' }}</div>
                    <div><span style="color:#64748b;">Alamat Pengiriman:</span><br>{{ $penjualan->alamat_pengiriman ?? '—' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3>💳 Info Pembayaran</h3></div>
            <div class="card-body">
                @php $bayar = $penjualan->pembayaran; @endphp
                @if($bayar)
                <div style="display:flex; flex-direction:column; gap:10px; font-size:13.5px;">
                    <div><span style="color:#64748b;">Metode:</span><br><strong>{{ strtoupper($bayar->metode_pembayaran ?? '—') }}</strong></div>
                    <div><span style="color:#64748b;">Status:</span><br>
                        @php $sc = ['pending'=>'badge-pending','settlement'=>'badge-success','cancel'=>'badge-danger']; @endphp
                        <span class="badge {{ $sc[$bayar->status_pembayaran] ?? 'badge-pending' }}">{{ ucfirst($bayar->status_pembayaran) }}</span>
                    </div>
                    <div><span style="color:#64748b;">Nominal:</span><br><strong style="color:#16a34a;">Rp {{ number_format($bayar->nominal_bayar, 0, ',', '.') }}</strong></div>
                    @if($bayar->waktu_bayar)
                    <div><span style="color:#64748b;">Waktu Bayar:</span><br>{{ \Carbon\Carbon::parse($bayar->waktu_bayar)->format('d M Y H:i') }}</div>
                    @endif
                </div>

                @if($penjualan->status_penjualan === 'menunggu_pembayaran')
                <div style="margin-top:16px; display:flex; gap:8px;">
                    <form action="{{ route('admin.penjualan.konfirmasi', $penjualan->id_penjualan) }}" method="POST" style="flex:1">
                        @csrf
                        <button class="btn btn-primary" style="width:100%;">✅ Konfirmasi Bayar</button>
                    </form>
                    <form action="{{ route('admin.penjualan.tolak', $penjualan->id_penjualan) }}" method="POST" onsubmit="return confirm('Tolak pesanan ini?')">
                        @csrf
                        <button class="btn btn-danger">❌</button>
                    </form>
                </div>
                @endif
                @else
                <p style="color:#94a3b8; font-size:13px;">Tidak ada data pembayaran.</p>
                @endif
            </div>
        </div>

        <div style="margin-top:16px;">
            <a href="{{ route('admin.penjualan.index') }}" class="btn btn-secondary" style="width:100%; justify-content:center;">← Kembali ke Daftar</a>
        </div>
    </div>
</div>
@endsection
