@extends('layouts.admin')
@section('title', 'Pembelian / Pasokan')
@section('page-title', 'Pembelian / Pasokan Beras')
@section('page-subtitle', 'Data masuk beras dari petani — setiap pasokan menciptakan batch FIFO baru')

@section('content')
<div class="card">
    <div class="card-header">
        <h3>📦 Daftar Pasokan Masuk</h3>
        <a href="{{ route('admin.pasokan.create') }}" class="btn btn-primary">+ Catat Pasokan Baru</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Tanggal</th><th>Petani / Pemasok</th><th>Produk</th><th>Volume</th><th>HPP/kg</th><th>Total</th><th>Dicatat Oleh</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($pasokans as $i => $ps)
                <tr>
                    <td style="color:#94a3b8;">{{ $pasokans->firstItem() + $i }}</td>
                    <td style="font-size:12.5px;">{{ \Carbon\Carbon::parse($ps->tgl_pasok)->format('d M Y') }}</td>
                    <td style="font-weight:700; font-size:13px;">{{ $ps->pemasok->nama_pemasok ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $ps->produk->nama_produk ?? '—' }}</td>
                    <td style="font-weight:700;">{{ number_format($ps->jumlah_kg) }} kg</td>
                    <td style="font-size:12.5px;">Rp {{ number_format($ps->harga_per_kg, 0, ',', '.') }}</td>
                    <td style="font-weight:700; color:#16a34a;">Rp {{ number_format($ps->total_bayar, 0, ',', '.') }}</td>
                    <td style="font-size:12px; color:#64748b;">{{ $ps->user->nama ?? 'Sistem' }}</td>
                    <td>
                        <form action="{{ route('admin.pasokan.destroy', $ps->id_pasokan) }}" method="POST" onsubmit="return confirm('Hapus data pasokan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center; color:#94a3b8; padding:32px;">Belum ada data pasokan. <a href="{{ route('admin.pasokan.create') }}">Catat sekarang</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pasokans->hasPages())
    <div style="padding: 16px 22px;">{{ $pasokans->links() }}</div>
    @endif
</div>
@endsection
