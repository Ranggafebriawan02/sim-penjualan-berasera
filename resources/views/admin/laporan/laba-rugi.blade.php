@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi FIFO')
@section('page-title', 'Laporan Laba Rugi FIFO')
@section('page-subtitle', 'Perhitungan laba rugi otomatis menggunakan metode First-In, First-Out (FIFO) berdasarkan batch pasokan')

@section('content')
<div style="display:flex; flex-direction:column; gap:24px;">

    {{-- Filter & Tombol Cetak --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size:16px; font-weight:700; color:#0f172a;">📅 Filter Rentang Penjualan</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Saring periode transaksi untuk menghitung omset & HPP FIFO</p>
            </div>
            <div>
                <button type="button" onclick="window.print()" class="btn btn-secondary btn-sm" style="font-weight:600; gap:8px;">
                    🖨️ Cetak Laporan (PDF)
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan.laba-rugi') }}" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)) 160px; gap:16px; align-items:flex-end;">
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
                    <a href="{{ route('admin.laporan.laba-rugi') }}" class="btn btn-secondary" style="justify-content:center;" title="Reset Filter">
                        🔄
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- 4 Kartu KPI Ringkasan Keuangan --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
        <div class="card" style="padding:20px; border-left:4px solid #059669;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total Omset Penjualan</div>
            <div style="font-size:24px; font-weight:800; color:#0f172a; margin-top:6px;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Nilai transaksi penjualan terverifikasi</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #d97706;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Total HPP FIFO</div>
            <div style="font-size:24px; font-weight:800; color:#b45309; margin-top:6px;">Rp {{ number_format($totalHPP, 0, ',', '.') }}</div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Harga pokok perolehan dari batch beras</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #10b981;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Laba Kotor Usaha</div>
            <div style="font-size:24px; font-weight:800; color:#047857; margin-top:6px;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</div>
            <div style="font-size:12px; color:#059669; font-weight:600; margin-top:4px;">Omset bersih dikurangi HPP FIFO</div>
        </div>

        <div class="card" style="padding:20px; border-left:4px solid #2563eb;">
            <div style="font-size:11.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px;">Margin Keuntungan</div>
            <div style="font-size:24px; font-weight:800; color:#1d4ed8; margin-top:6px;">{{ number_format($marginPersen, 1) }}%</div>
            <div style="font-size:12px; color:#64748b; margin-top:4px;">Rasio efisiensi laba kotor</div>
        </div>
    </div>

    {{-- Tabel Rincian FIFO --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3>📊 Rincian Laba Rugi per Transaksi Penjualan</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Data otomatis terhubung dengan database View `view_laporan_laba_rugi_fifo`</p>
            </div>
            <span style="font-size:12px; font-weight:600; background:#f1f5f9; padding:5px 12px; border-radius:8px; color:#475569;">
                Periode: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}
            </span>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:170px;">No. Faktur</th>
                        <th>Tanggal Jual</th>
                        <th>Nama Pembeli</th>
                        <th>Metode Bayar</th>
                        <th style="text-align:right;">Omset (Rp)</th>
                        <th style="text-align:right;">HPP FIFO (Rp)</th>
                        <th style="text-align:right;">Laba Kotor (Rp)</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fifoData as $item)
                    <tr>
                        <td>
                            <strong style="font-family:monospace; font-size:12.5px; color:#0f172a;">{{ $item->id_penjualan }}</strong>
                        </td>
                        <td style="font-size:13px; color:#475569;">
                            {{ \Carbon\Carbon::parse($item->tgl_jual)->format('d M Y') }}
                        </td>
                        <td>
                            <div style="font-weight:700; color:#0f172a; font-size:13.5px;">{{ $item->nama_customer ?? 'Pelanggan Umum' }}</div>
                        </td>
                        <td>
                            <span style="font-size:11.5px; font-weight:700; color:#1e40af; background:#dbeafe; padding:2px 8px; border-radius:6px; text-transform:uppercase;">
                                {{ str_replace('_', ' ', $item->metode_transaksi) }}
                            </span>
                        </td>
                        <td style="text-align:right; font-weight:700; color:#0f172a; font-size:14px;">
                            Rp {{ number_format($item->omset_penjualan, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right; font-weight:600; color:#b45309; font-size:13.5px;">
                            Rp {{ number_format($item->total_hpp_fifo, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right; font-weight:800; font-size:14px; color:{{ $item->laba_kotor_fifo >= 0 ? '#047857' : '#dc2626' }};">
                            Rp {{ number_format($item->laba_kotor_fifo, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            <span class="badge badge-success">{{ ucfirst($item->status_penjualan) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:36px; color:#94a3b8;">
                            Belum ada catatan transaksi penjualan pada rentang tanggal tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($fifoData) > 0)
                <tfoot style="background:#f8fafc; font-weight:700;">
                    <tr>
                        <td colspan="4" style="text-align:right; text-transform:uppercase; font-size:12px; color:#475569; padding:14px;">Total Akumulasi:</td>
                        <td style="text-align:right; font-size:14px; color:#0f172a; padding:14px;">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                        <td style="text-align:right; font-size:14px; color:#b45309; padding:14px;">Rp {{ number_format($totalHPP, 0, ',', '.') }}</td>
                        <td style="text-align:right; font-size:15px; color:#047857; padding:14px;">Rp {{ number_format($labaKotor, 0, ',', '.') }}</td>
                        <td style="padding:14px;"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
