@extends('layouts.admin')
@section('title', 'Transaksi Penjualan')
@section('page-title', 'Transaksi Penjualan')
@section('page-subtitle', 'Kelola dan monitor semua pesanan pelanggan')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>🛒 Daftar Transaksi</h3>
        <div style="display:flex; gap:8px;">
            <form method="GET" style="display:flex; gap:8px; align-items:center;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width:180px; padding:7px 12px;" placeholder="Cari ID / Pembeli...">
                <select name="status" class="form-control" style="width:160px; padding:7px 12px;">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pembayaran" {{ request('status') == 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu</option>
                    <option value="diverifikasi" {{ request('status') == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm">🔍 Filter</button>
            </form>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>No. Faktur</th><th>Tanggal</th><th>Pembeli</th><th>Total</th><th>Metode</th><th>Pembayaran</th><th>Status Pesanan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($penjualans as $pj)
                <tr>
                    <td>
                        <a href="{{ route('admin.penjualan.show', $pj->id_penjualan) }}" style="color:#16a34a; font-weight:700; font-size:12.5px;">
                            {{ $pj->id_penjualan }}
                        </a>
                    </td>
                    <td style="font-size:12.5px;">{{ \Carbon\Carbon::parse($pj->tgl_jual)->format('d M Y') }}</td>
                    <td style="font-size:13px; font-weight:600;">{{ $pj->customer->nama_customer ?? '—' }}</td>
                    <td style="font-weight:700;">Rp {{ number_format($pj->total, 0, ',', '.') }}</td>
                    <td style="font-size:12px;">{{ $pj->metode_transaksi ?? '—' }}</td>
                    <td>
                        @php
                            $bayarStatus = $pj->pembayaran->status_pembayaran ?? 'pending';
                            $bc = ['pending'=>'badge-pending','settlement'=>'badge-success','cancel'=>'badge-danger','expire'=>'badge-danger'];
                        @endphp
                        <span class="badge {{ $bc[$bayarStatus] ?? 'badge-pending' }}">{{ ucfirst($bayarStatus) }}</span>
                    </td>
                    <td>
                        @php
                            $sc = ['menunggu_pembayaran'=>'badge-pending','diverifikasi'=>'badge-success','diproses'=>'badge-info','dikirim'=>'badge-info','selesai'=>'badge-success','dibatalkan'=>'badge-danger'];
                            $label = ['menunggu_pembayaran'=>'Menunggu','diverifikasi'=>'Terverif','diproses'=>'Diproses','dikirim'=>'Dikirim','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'];
                        @endphp
                        <span class="badge {{ $sc[$pj->status_penjualan] ?? 'badge-pending' }}">{{ $label[$pj->status_penjualan] ?? $pj->status_penjualan }}</span>
                    </td>
                    <td>
                        <div style="display:flex; gap:4px;">
                            <a href="{{ route('admin.penjualan.show', $pj->id_penjualan) }}" class="btn btn-sm btn-secondary">👁</a>
                            @if($pj->status_penjualan === 'menunggu_pembayaran')
                            <form action="{{ route('admin.penjualan.konfirmasi', $pj->id_penjualan) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-primary" title="Konfirmasi Bayar">✅</button>
                            </form>
                            <form action="{{ route('admin.penjualan.tolak', $pj->id_penjualan) }}" method="POST" onsubmit="return confirm('Tolak pesanan ini?')">
                                @csrf
                                <button class="btn btn-sm btn-danger" title="Tolak">❌</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:32px;">Tidak ada transaksi yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penjualans->hasPages())
    <div style="padding: 16px 22px;">{{ $penjualans->links() }}</div>
    @endif
</div>
@endsection
