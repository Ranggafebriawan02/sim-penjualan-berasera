@extends('layouts.admin')

@section('title', 'Dashboard — Berasera SIM')
@section('page-title', 'Dashboard Eksekutif')
@section('page-subtitle', 'Selamat datang, ' . Auth::guard('web')->user()->nama . ' (' . ucfirst(Auth::guard('web')->user()->level) . ')')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #eef2f6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-sub {
        font-size: 11.5px;
        color: #94a3b8;
    }

    .section-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .section-grid-3 {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .section-full {
        margin-bottom: 20px;
    }

    .alert-stok {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
    }

    .alert-stok-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
    }

    .alert-stok-item .stok-val {
        margin-left: auto;
        font-weight: 700;
        font-size: 13px;
        color: #d97706;
    }

    .fifo-badge {
        font-size: 10.5px;
        padding: 2px 7px;
        border-radius: 8px;
        background: #ecfdf5;
        color: #047857;
        font-weight: 600;
        border: 1px solid #a7f3d0;
    }

    @media(max-width: 900px) {
        .section-grid, .section-grid-3 { grid-template-columns: 1fr; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ===== ANALITIK CERDAS ===== */
    .insight-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 16px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-size: 13.5px;
        line-height: 1.5;
        border-left: 4px solid;
    }
    .insight-success { background: #f0fdf4; border-color: #22c55e; color: #166534; }
    .insight-warning { background: #fffbeb; border-color: #f59e0b; color: #92400e; }
    .insight-danger  { background: #fef2f2; border-color: #ef4444; color: #991b1b; }
    .insight-info    { background: #eff6ff; border-color: #3b82f6; color: #1e40af; }

    .growth-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .growth-up   { background: #dcfce7; color: #16a34a; }
    .growth-down { background: #fee2e2; color: #dc2626; }
    .growth-flat { background: #f1f5f9; color: #64748b; }

    .rank-badge {
        width: 26px; height: 26px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 12px; flex-shrink: 0;
    }
    .rank-1 { background: #fef3c7; color: #92400e; }
    .rank-2 { background: #f1f5f9; color: #475569; }
    .rank-3 { background: #fdf4ff; color: #7e22ce; }
    .rank-other { background: #f0fdf4; color: #166534; }

    .progress-bar-wrap {
        background: #f1f5f9;
        border-radius: 99px;
        height: 6px;
        margin-top: 4px;
        overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #10b981, #059669);
        transition: width 0.6s ease;
    }

    .tier-badge {
        font-size: 10px; font-weight: 700;
        padding: 2px 7px; border-radius: 8px;
    }
    .tier-Silver   { background: #f1f5f9; color: #64748b; }
    .tier-Gold     { background: #fef3c7; color: #92400e; }
    .tier-Platinum { background: #ede9fe; color: #6d28d9; }

    .analitik-section { margin-bottom: 24px; }
    .aov-highlight {
        text-align: center;
        padding: 20px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 14px;
        margin-bottom: 16px;
    }
</style>
{{-- Library Chart.js via CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')

{{-- Peringatan Stok --}}
@if($stokMenipis->count() > 0)
<div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 16px 20px; margin-bottom: 24px;">
    <div style="display:flex; align-items:center; gap:8px; margin-bottom: 12px;">
        <span style="font-size:20px;">⚠️</span>
        <strong style="color:#92400e; font-size:14.5px;">Peringatan Restock: {{ $stokMenipis->count() }} varian beras berada di bawah 50 Kg!</strong>
        <a href="{{ route('admin.laporan.stok') }}" class="btn btn-sm btn-warning" style="margin-left:auto;">Detail Stok</a>
    </div>
    <div class="alert-stok">
        @foreach($stokMenipis->take(4) as $p)
        <div class="alert-stok-item">
            <span style="font-size:16px;">🌾</span>
            <div>
                <div style="font-size:13px; font-weight:700; color:#0f172a;">{{ Str::limit($p->nama_produk, 22) }}</div>
                <div style="font-size:11px; color:#92400e;">{{ $p->kategori->nama_kategori ?? '—' }}</div>
            </div>
            <span class="stok-val">{{ number_format($p->stok) }} kg</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Stats Grid Utama --}}
<div class="stats-grid">
    {{-- Card 1: Total Omset --}}
    <div class="stat-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div class="stat-label">Total Omset Penjualan</div>
            <div class="stat-icon" style="background:#dcfce7; color:#15803d;">💰</div>
        </div>
        <div class="stat-value" style="color:#059669;">Rp {{ number_format($totalOmset, 0, ',', '.') }}</div>
        <div class="stat-sub">Dari total {{ number_format($totalTransaksi) }} pesanan berhasil</div>
    </div>

    {{-- Card 2: Laba Kotor Bulan Ini (FIFO) --}}
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div class="stat-label">Laba Kotor (Bulan Ini)</div>
            <div class="stat-icon" style="background:#ecfdf5; color:#047857;">📈</div>
        </div>
        <div class="stat-value" style="color:#047857;">Rp {{ number_format($labaBulanIni, 0, ',', '.') }}</div>
        <div class="stat-sub">Omset Rp {{ number_format($omsetBulanIni, 0, ',', '.') }} - HPP FIFO</div>
    </div>

    {{-- Card 3: Total Persediaan Beras --}}
    <div class="stat-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div class="stat-label">Persediaan Gudang</div>
            <div class="stat-icon" style="background:#eff6ff; color:#2563eb;">📦</div>
        </div>
        <div class="stat-value">{{ number_format($totalStokKg) }} <span style="font-size:14px; font-weight:500; color:#64748b;">Kg</span></div>
        <div class="stat-sub">{{ number_format($totalProduk) }} varian aktif terdaftar</div>
    </div>

    {{-- Card 4: Mitra Petani & Pembeli --}}
    <div class="stat-card">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div class="stat-label">Ekosistem Poktan</div>
            <div class="stat-icon" style="background:#fdf4ff; color:#a21caf;">🧑‍🤝‍🧑</div>
        </div>
        <div class="stat-value">{{ number_format($totalCustomer) }} <span style="font-size:13px; font-weight:500; color:#64748b;">Pembeli</span></div>
        <div class="stat-sub">{{ number_format($totalPemasok) }} Mitra Petani aktif</div>
    </div>
</div>

{{-- SECTION GRAFIK INTERAKTIF CHART.JS --}}
<div class="section-grid-3">
    {{-- Grafik Tren Penjualan 7 Hari --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3>📈 Tren Omset Penjualan 7 Hari Terakhir</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Grafik interaktif pendapatan transaksi beras per hari</p>
            </div>
            <span style="font-size:12px; font-weight:700; color:#059669; background:#ecfdf5; padding:4px 10px; border-radius:8px;">
                Total: Rp {{ number_format($chartPenjualan->sum('total_omset'), 0, ',', '.') }}
            </span>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 260px; width: 100%;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Donut Chart Proporsi Penjualan Varietas --}}
    <div class="card">
        <div class="card-header">
            <h3>🌾 Penjualan per Varietas</h3>
            <p style="font-size:12px; color:#64748b; margin-top:2px;">Distribusi volume kg beras terjual</p>
        </div>
        <div class="card-body">
            <div style="position: relative; height: 260px; width: 100%; display:flex; align-items:center; justify-content:center;">
                <canvas id="categoryDonutChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Transaksi & Antrean FIFO Aktif --}}
<div class="section-grid">
    {{-- Transaksi Terbaru --}}
    <div class="card">
        <div class="card-header">
            <h3>🛒 Transaksi Penjualan Terbaru</h3>
            <a href="{{ route('admin.penjualan.index') }}" class="btn btn-sm btn-outline">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Faktur</th><th>Pembeli</th><th>Total Transaksi</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $t)
                    <tr>
                        <td><a href="{{ route('admin.penjualan.show', $t->id_penjualan) }}" style="font-size:12px; color:#16a34a; font-weight:700; font-family:monospace;">{{ $t->id_penjualan }}</a></td>
                        <td style="font-size:12.5px; font-weight:600;">{{ $t->customer->nama_customer ?? '—' }}</td>
                        <td style="font-size:12.5px; font-weight:700;">Rp {{ number_format($t->total,0,',','.') }}</td>
                        <td>
                            @php
                                $sc = ['menunggu_pembayaran'=>'badge-pending','diverifikasi'=>'badge-success','diproses'=>'badge-info','dikirim'=>'badge-info','selesai'=>'badge-success','dibatalkan'=>'badge-danger'];
                                $label = ['menunggu_pembayaran'=>'Menunggu','diverifikasi'=>'Terverif','diproses'=>'Diproses','dikirim'=>'Dikirim','selesai'=>'Selesai','dibatalkan'=>'Batal'];
                            @endphp
                            <span class="badge {{ $sc[$t->status_penjualan] ?? 'badge-pending' }}">{{ $label[$t->status_penjualan] ?? $t->status_penjualan }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:24px;">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Antrean Batch FIFO Aktif --}}
    <div class="card">
        <div class="card-header">
            <h3>📦 Antrean Batch FIFO Aktif (Gudang)</h3>
            <a href="{{ route('admin.laporan.stok') }}" class="btn btn-sm btn-outline">Detail Stok</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Varian & Batch</th><th>Masuk</th><th>Sisa Stok</th><th>Aksi Antrean</th></tr>
                </thead>
                <tbody>
                    @forelse($fifoBatches as $b)
                    <tr>
                        <td>
                            <div style="font-size:12.5px; font-weight:700;">{{ Str::limit($b->produk->nama_produk ?? '—', 22) }}</div>
                            <span class="fifo-badge">{{ $b->kode_batch }}</span>
                        </td>
                        <td style="font-size:12px; color:#64748b;">{{ \Carbon\Carbon::parse($b->tgl_masuk)->format('d/m/Y') }}</td>
                        <td style="font-size:13px; font-weight:800; color:{{ $b->stok_sisa < 20 ? '#ef4444' : '#059669' }};">
                            {{ number_format($b->stok_sisa) }} Kg
                        </td>
                        <td><span class="badge badge-success" style="font-size:10px;">Prioritas Keluar</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:24px;">Tidak ada batch aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Pasokan Petani Terbaru --}}
<div class="card section-full">
    <div class="card-header">
        <h3>🚜 Pasokan Beras Petani Terbaru</h3>
        <a href="{{ route('admin.pasokan.index') }}" class="btn btn-sm btn-outline">Semua Pasokan</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Petani / Pemasok</th><th>Produk Beras</th><th>Volume</th><th>Harga Beli/Kg</th><th>Total Pembayaran</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($pasokanTerbaru as $ps)
                <tr>
                    <td style="font-size:12.5px;">{{ \Carbon\Carbon::parse($ps->tgl_pasok)->format('d M Y') }}</td>
                    <td style="font-size:12.5px; font-weight:600;">{{ $ps->pemasok->nama_pemasok ?? '—' }}</td>
                    <td style="font-size:12.5px;">{{ $ps->produk->nama_produk ?? '—' }}</td>
                    <td style="font-size:12.5px; font-weight:700; color:#2563eb;">{{ number_format($ps->jumlah_kg) }} Kg</td>
                    <td style="font-size:12.5px;">Rp {{ number_format($ps->harga_per_kg,0,',','.') }}</td>
                    <td style="font-size:13px; font-weight:800;">Rp {{ number_format($ps->total_bayar,0,',','.') }}</td>
                    <td>
                        <span class="badge {{ $ps->status_pembayaran_petani === 'lunas' ? 'badge-success' : 'badge-pending' }}">
                            {{ ucfirst(str_replace('_', ' ', $ps->status_pembayaran_petani)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:24px;">Belum ada data pasokan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SEKSI ANALISIS KECERDASAN PENJUALAN --}}
{{-- ============================================================ --}}

{{-- Divider --}}
<div style="display:flex; align-items:center; gap:12px; margin: 8px 0 20px;">
    <div style="flex:1; height:1px; background:#e2e8f0;"></div>
    <span style="font-size:13px; font-weight:700; color:#64748b; white-space:nowrap; display:flex; align-items:center; gap:6px;">
        🤖 ANALISIS KECERDASAN PENJUALAN
    </span>
    <div style="flex:1; height:1px; background:#e2e8f0;"></div>
</div>

{{-- Insight Otomatis (AI Recommendations) --}}
<div class="card analitik-section">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
            <h3>💡 Rekomendasi & Insight Otomatis</h3>
            <p style="font-size:12px; color:#64748b; margin-top:2px;">Analisis berbasis data real-time untuk pengambilan keputusan</p>
        </div>
        <span style="font-size:11px; background:#ecfdf5; color:#059669; padding:4px 10px; border-radius:8px; font-weight:700;">
            {{ count($insightList) }} insight ditemukan
        </span>
    </div>
    <div class="card-body">
        @forelse($insightList as $insight)
        <div class="insight-card insight-{{ $insight['type'] }}">
            <span style="font-size:20px; line-height:1;">{{ $insight['icon'] }}</span>
            <div>{!! $insight['msg'] !!}</div>
        </div>
        @empty
        <div style="text-align:center; color:#94a3b8; padding:20px;">Belum ada insight yang tersedia.</div>
        @endforelse
    </div>
</div>

{{-- Tren 6 Bulan + Perbandingan Bulan --}}
<div class="section-grid analitik-section">

    {{-- Tren Omset 6 Bulan Terakhir --}}
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3>📅 Tren Omset 6 Bulan Terakhir</h3>
                <p style="font-size:12px; color:#64748b; margin-top:2px;">Grafik pertumbuhan penjualan bulanan</p>
            </div>
            @php
                $growthIcon = $pertumbuhanOmset >= 0 ? '▲' : '▼';
                $growthClass = $pertumbuhanOmset >= 10 ? 'growth-up' : ($pertumbuhanOmset < 0 ? 'growth-down' : 'growth-flat');
            @endphp
            <span class="growth-badge {{ $growthClass }}">
                {{ $growthIcon }} {{ abs($pertumbuhanOmset) }}% vs bln lalu
            </span>
        </div>
        <div class="card-body">
            <div style="position:relative; height:220px;">
                <canvas id="trenBulananChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Analisis Metode Pembayaran + AOV --}}
    <div class="card">
        <div class="card-header">
            <h3>💳 Analisis Metode Pembayaran</h3>
            <p style="font-size:12px; color:#64748b; margin-top:2px;">Preferensi pembayaran pelanggan</p>
        </div>
        <div class="card-body">
            <div class="aov-highlight">
                <div style="font-size:11px; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:0.5px;">Rata-rata Nilai Transaksi (AOV)</div>
                <div style="font-size:26px; font-weight:900; color:#047857;">Rp {{ number_format($aovBulanIni, 0, ',', '.') }}</div>
                <div style="font-size:11px; color:#6b7280;">Bulan {{ now()->translatedFormat('F Y') }}</div>
            </div>
            <div style="position:relative; height:155px;">
                <canvas id="metodeChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Produk Terlaris + Pelanggan Loyal --}}
<div class="section-grid analitik-section">

    {{-- Top 5 Produk Terlaris Bulan Ini --}}
    <div class="card">
        <div class="card-header">
            <h3>🏆 Produk Terlaris Bulan Ini</h3>
            <span style="font-size:11px; background:#fef9c3; color:#92400e; padding:3px 8px; border-radius:6px; font-weight:600;">
                {{ now()->translatedFormat('F Y') }}
            </span>
        </div>
        <div class="card-body">
            @php $maxKg = $produkTerlaris->max('total_kg') ?: 1; @endphp
            @forelse($produkTerlaris as $i => $prod)
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <div class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:3px;">
                        <span style="font-size:13px; font-weight:700; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:160px;">
                            {{ $prod->nama_produk }}
                        </span>
                        <span style="font-size:12px; font-weight:800; color:#059669; flex-shrink:0; margin-left:6px;">
                            {{ number_format($prod->total_kg) }} Kg
                        </span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width: {{ min(100, round(($prod->total_kg / $maxKg) * 100)) }}%;"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:3px;">
                        <span style="font-size:11px; color:#64748b;">{{ $prod->jumlah_transaksi }} transaksi</span>
                        <span style="font-size:11px; font-weight:600; color:#475569;">Rp {{ number_format($prod->total_omset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#94a3b8; padding:30px 0;">Belum ada data penjualan bulan ini.</div>
            @endforelse
        </div>
    </div>

    {{-- Top 5 Pelanggan Paling Loyal --}}
    <div class="card">
        <div class="card-header">
            <h3>⭐ Pelanggan Paling Loyal</h3>
            <a href="{{ route('admin.users.customer') }}" class="btn btn-sm btn-outline">Semua Pelanggan</a>
        </div>
        <div class="card-body">
            @php $maxBelanja = $pelangganLoyal->max('total_belanja') ?: 1; @endphp
            @forelse($pelangganLoyal as $i => $pel)
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <div class="rank-badge {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-other')) }}">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3px;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; font-weight:700; color:#0f172a;">{{ $pel->nama_customer }}</span>
                            <span class="tier-badge tier-{{ $pel->tier_membership }}">{{ $pel->tier_membership }}</span>
                        </div>
                        <span style="font-size:11.5px; font-weight:800; color:#059669; flex-shrink:0; margin-left:6px;">
                            Rp {{ number_format($pel->total_belanja, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill" style="width: {{ min(100, round(($pel->total_belanja / $maxBelanja) * 100)) }}%; background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-top:3px;">
                        <span style="font-size:11px; color:#64748b;">{{ $pel->jumlah_order }} order</span>
                        <span style="font-size:11px; color:#f59e0b; font-weight:600;">⭐ {{ number_format($pel->total_poin) }} poin</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#94a3b8; padding:30px 0;">Belum ada data pelanggan.</div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // 1. Inisialisasi Line/Bar Chart Penjualan 7 Hari
    const dates = @json($chartPenjualan->pluck('tanggal')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M')));
    const revenues = @json($chartPenjualan->pluck('total_omset'));
    const orderCounts = @json($chartPenjualan->pluck('jumlah_order'));

    const ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: dates.length ? dates : ['Tidak ada data'],
            datasets: [
                {
                    label: 'Omset (Rp)',
                    data: revenues.length ? revenues : [0],
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderRadius: 8,
                    yAxisID: 'y'
                },
                {
                    label: 'Jumlah Order',
                    data: orderCounts.length ? orderCounts : [0],
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(val) { return 'Rp ' + val.toLocaleString('id-ID'); }
                    },
                    grid: { color: '#f1f5f9' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }
                },
                x: { grid: { display: false } }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.yAxisID === 'y') {
                                return 'Omset: Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                            return 'Pesanan: ' + context.raw + ' order';
                        }
                    }
                }
            }
        }
    });

    // 2. Inisialisasi Donut Chart Varietas
    const catLabels = @json($kategoriSales->pluck('nama_kategori'));
    const catKg = @json($kategoriSales->pluck('total_kg'));

    const ctxCat = document.getElementById('categoryDonutChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: catLabels.length ? catLabels : ['Belum ada'],
            datasets: [{
                data: catKg.length ? catKg : [1],
                backgroundColor: ['#ef4444', '#1e293b', '#10b981', '#3b82f6', '#f59e0b'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + ' Kg';
                        }
                    }
                }
            }
        }
    });

    // 3. Tren Omset 6 Bulan Terakhir (Analisis Cerdas)
    const bulanLabels = @json($trenBulanan->pluck('label'));
    const bulanOmset = @json($trenBulanan->pluck('total_omset'));
    const bulanOrder = @json($trenBulanan->pluck('jumlah_order'));

    const ctxTren = document.getElementById('trenBulananChart');
    if (ctxTren) {
        new Chart(ctxTren.getContext('2d'), {
            type: 'line',
            data: {
                labels: bulanLabels.length ? bulanLabels : ['Bulan Ini'],
                datasets: [
                    {
                        label: 'Omset Penjualan (Rp)',
                        data: bulanOmset.length ? bulanOmset : [0],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 5,
                        pointBackgroundColor: '#059669',
                        borderWidth: 3,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Pesanan',
                        data: bulanOrder.length ? bulanOrder : [0],
                        type: 'bar',
                        backgroundColor: 'rgba(59, 130, 246, 0.4)',
                        borderRadius: 6,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            callback: function(v) { return 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : v.toLocaleString('id-ID')); }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false }
                    },
                    x: { grid: { display: false } }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'y') {
                                    return 'Omset: Rp ' + Number(context.raw).toLocaleString('id-ID');
                                }
                                return 'Pesanan: ' + context.raw + ' order';
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Analisis Metode Pembayaran (Doughnut)
    const metodeRaw = @json($analisisMetode);
    const mapNamaMetode = {
        'midtrans_qris': 'QRIS Midtrans',
        'midtrans_va': 'Virtual Account',
        'cash': 'Tunai (COD/Kasir)'
    };
    const mLabels = metodeRaw.map(m => mapNamaMetode[m.metode_transaksi] || m.metode_transaksi);
    const mData = metodeRaw.map(m => m.jumlah);

    const ctxMetode = document.getElementById('metodeChart');
    if (ctxMetode) {
        new Chart(ctxMetode.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: mLabels.length ? mLabels : ['Belum Ada'],
                datasets: [{
                    data: mData.length ? mData : [1],
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 10, font: { size: 10 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.raw + ' transaksi';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endsection
