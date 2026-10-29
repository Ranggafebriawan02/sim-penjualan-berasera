@extends('layouts.admin')

@section('title', 'Laporan Pembelian & Pasokan Beras')
@section('page-title', 'Laporan Pembelian & Pasokan')
@section('page-subtitle', 'Rekapitulasi pasokan beras masuk dari petani mitra Poktan Gapsera Sejahtera Mandiri')

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Filter & Aksi Bar --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size:16px; font-weight:700; color:#0f172a;">📅 Filter Periode Laporan</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Pilih rentang tanggal masuk gudang</p>
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="font-weight:600; gap:8px;">
                    🖨️ Cetak Laporan (PDF)
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.pembelian') }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 160px; gap:16px; align-items:flex-end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="form-control" required>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center;">
                        🔍 Filter
                    </button>
                    <a href="{{ route('admin.laporan.pembelian') }}" class="btn btn-secondary" style="justify-content:center;" title="Reset Filter">
                        🔄
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 4 Kartu KPI Ringkasan --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
        <div class="card" style="padding:20px; border-left:4px solid #059669;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Biaya Pembelian</div>
            <div style="font-size:24px; font-weight:800; color:#065f46; margin-top:6px;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Total dana dibayarkan ke petani</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #2563eb;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Volume Masuk</div>
            <div style="font-size:24px; font-weight:800; color:#1d4ed8; margin-top:6px;">{{ number_format($totalVolume, 0, ',', '.') }} <span style="font-size:15px; font-weight:600; color:#64748b;">Kg</span></div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Akumulasi berat beras masuk</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #7c3aed;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Frekuensi Pasokan</div>
            <div style="font-size:24px; font-weight:800; color:#6d28d9; margin-top:6px;">{{ $pasokans->count() }} <span style="font-size:15px; font-weight:600; color:#64748b;">Batch</span></div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Jumlah transaksi pengiriman</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #d97706;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Rata-rata Harga Beli</div>
            <div style="font-size:24px; font-weight:800; color:#b45309; margin-top:6px;">
                Rp {{ $totalVolume > 0 ? number_format($totalPembelian / $totalVolume, 0, ',', '.') : 0 }}
                <span style="font-size:14px; font-weight:600; color:#64748b;">/Kg</span>
            </div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">HPP rata-rata per Kilogram</div>
        </div>
    </div>

    {{-- Tabel Data Pasokan Beras --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3>📦 Rincian Pasokan Masuk dari Petani</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Data pasokan beras dengan kode batch FIFO aktif</p>
            </div>
            <span style="font-size:12px; font-weight:600; background:#f1f5f9; padding:5px 12px; border-radius:8px; color:#475569;">
                Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}
            </span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:130px;">ID & Batch</th>
                        <th>Tanggal Pasok</th>
                        <th>Petani / Pemasok</th>
                        <th>Produk Beras</th>
                        <th style="text-align:right;">Volume (Kg)</th>
                        <th style="text-align:right;">Harga Beli / Kg</th>
                        <th style="text-align:right;">Total Pembayaran</th>
                        <th style="text-align:center;">Status Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pasokans as $p)
                    <tr>
                        <td>
                            <strong style="font-family:monospace; font-size:12.5px; color:#0f172a;">PSK-{{ str_pad($p->id_pasokan, 4, '0', STR_PAD_LEFT) }}</strong>
                            @if(isset($p->batch->kode_batch))
                                <div style="font-size:11px; font-family:monospace; color:#059669;">{{ $p->batch->kode_batch }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px; color:#475569;">
                            {{ \Carbon\Carbon::parse($p->tgl_pasok)->format('d M Y') }}
                        </td>
                        <td>
                            <div style="font-weight:700; color:#0f172a; font-size:13.5px;">{{ $p->pemasok->nama_pemasok ?? '—' }}</div>
                            <div style="font-size:11.5px; color:#64748b;">{{ $p->pemasok->kontak ?? $p->pemasok->no_hp ?? 'Petani Mitra' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600; color:#0f172a;">{{ $p->produk->nama_produk ?? '—' }}</div>
                            @if(isset($p->produk->kategori))
                                <span style="font-size:11px; color:#059669; font-weight:600; background:#ecfdf5; padding:2px 8px; border-radius:12px; display:inline-block; margin-top:2px;">
                                    {{ $p->produk->kategori->nama_kategori }}
                                </span>
                            @endif
                        </td>
                        <td style="text-align:right; font-weight:700; color:#0f172a; font-size:14px;">
                            {{ number_format($p->jumlah_kg, 0, ',', '.') }} Kg
                        </td>
                        <td style="text-align:right; font-size:13px; color:#475569;">
                            Rp {{ number_format($p->harga_per_kg, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right; font-weight:800; font-size:14px; color:#065f46;">
                            Rp {{ number_format($p->total_bayar, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            @if($p->status_pembayaran_petani === 'lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-warning">Belum Lunas</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px; color:#94a3b8;">
                            Belum ada catatan pasokan pada rentang tanggal tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($pasokans->count() > 0)
                <tfoot style="background:#f8fafc; font-weight:700;">
                    <tr>
                        <td colspan="4" style="text-align:right; text-transform:uppercase; font-size:12px; color:#475569; padding:14px;">Total Keseluruhan:</td>
                        <td style="text-align:right; font-size:14px; color:#1d4ed8; padding:14px;">{{ number_format($totalVolume, 0, ',', '.') }} Kg</td>
                        <td style="padding:14px;"></td>
                        <td style="text-align:right; font-size:15px; color:#065f46; padding:14px;">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                        <td style="padding:14px;"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
