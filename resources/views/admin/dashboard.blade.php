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
</script>
@endsection
