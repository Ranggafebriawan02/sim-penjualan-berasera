@extends('layouts.app')

@section('title', 'Katalog Beras Organik Berasera - Poktan Gapsera Sejahtera Mandiri')

@section('styles')
<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
        color: white;
        padding: 60px 28px;
        position: relative;
        overflow: hidden;
    }

    .hero-container {
        max-width: 1280px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        align-items: center;
        gap: 40px;
    }

    .hero-badges {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        color: #a7f3d0;
        font-size: 12px;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero h1 {
        font-size: 38px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 16px;
        letter-spacing: -1px;
    }

    .hero h1 span {
        color: #fde047;
    }

    .hero p {
        font-size: 16px;
        color: #d1fae5;
        margin-bottom: 26px;
        line-height: 1.7;
    }

    .hero-stats {
        display: flex;
        gap: 24px;
        background: rgba(0, 0, 0, 0.2);
        padding: 18px 24px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stat-item h3 {
        font-size: 22px;
        font-weight: 800;
        color: #fde047;
    }

    .stat-item p {
        font-size: 12px;
        color: #a7f3d0;
        margin-bottom: 0;
    }

    .hero-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 30px;
        color: white;
        text-align: center;
    }

    .hero-card-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    /* Filter Bar */
    .filter-section {
        max-width: 1280px;
        margin: -25px auto 40px auto;
        padding: 0 24px;
        position: relative;
        z-index: 10;
    }

    .filter-box {
        background: white;
        border-radius: 16px;
        padding: 20px 28px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: center;
        justify-content: space-between;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-btn {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid transparent;
        transition: all 0.2s;
    }

    .filter-btn:hover, .filter-btn.active {
        background: #059669;
        color: white;
    }

    /* Product Grid */
    .catalog-section {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    .section-header {
        margin-bottom: 28px;
    }

    .section-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }

    .section-header p {
        color: #64748b;
        font-size: 14px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 28px;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
        border-color: #10b981;
    }

    .product-img-wrap {
        height: 180px;
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 64px;
        position: relative;
    }

    .grade-tag {
        position: absolute;
        top: 14px;
        right: 14px;
        background: #064e3b;
        color: #34d399;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .product-body {
        padding: 22px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .product-cat {
        font-size: 12px;
        font-weight: 700;
        color: #059669;
        text-transform: uppercase;
        margin-bottom: 6px;
    }

    .product-title {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .product-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 16px;
        flex: 1;
    }

    .product-specs {
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 12px;
        margin-bottom: 16px;
        color: #334155;
    }

    .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: 1px solid #f1f5f9;
    }

    .product-price {
        font-size: 20px;
        font-weight: 800;
        color: #064e3b;
    }

    .product-price small {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .stock-indicator {
        font-size: 12px;
        font-weight: 600;
        color: #059669;
    }

    .btn-buy {
        background: #059669;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13.5px;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-buy:hover {
        background: #047857;
    }

    /* FIFO Banner Section */
    .info-banner {
        max-width: 1280px;
        margin: 60px auto 0 auto;
        padding: 0 24px;
    }

    .fifo-box {
        background: #111827;
        color: white;
        border-radius: 20px;
        padding: 35px 40px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: center;
    }

    .fifo-box h3 {
        font-size: 22px;
        font-weight: 800;
        color: #38bdf8;
        margin-bottom: 12px;
    }

    .fifo-box p {
        font-size: 14px;
        color: #94a3b8;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .hero-container {
            grid-template-columns: 1fr;
        }
        .fifo-box {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')

    <!-- Hero Banner -->
    <section class="hero">
        <div class="hero-container">
            <div>
                <div class="hero-badges">
                    <span class="hero-badge">🌱 100% Organik Bersertifikat</span>
                    <span class="hero-badge">📦 Sistem Mutu FIFO</span>
                    <span class="hero-badge">🌾 Petani Poktan Gapsera Sejahtera Mandiri</span>
                </div>
                <h1>Beras Organik Segar Berkualitas, <span>Langsung dari Petani</span> Metro</h1>
                <p>
                    Nikmati beras organik bebas pestisida kimia sintetis yang dibudidayakan secara lestari oleh kelompok tani Gapsera Sejahtera Mandiri. Diproses dengan standar mutu ketat, kemasan vakum higienis, dan pengiriman cepat.
                </p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>3 Varietas</h3>
                        <p>Merah, Hitam, Mentik Wangi</p>
                    </div>
                    <div class="stat-item">
                        <h3>Grade A</h3>
                        <p>Patah &le; 5% &amp; Vakum</p>
                    </div>
                    <div class="stat-item">
                        <h3>1 Poin / 10rb</h3>
                        <p>Reward Poin Belanja</p>
                    </div>
                </div>
            </div>

            <div class="hero-card">
                <div class="hero-card-icon">🍚</div>
                <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 8px;">Pesan Online Mudah &amp; Cepat</h3>
                <p style="font-size: 13.5px; color: #a7f3d0; margin-bottom: 18px;">
                    Dukungan pembayaran digital instan via <strong>Midtrans QRIS, Transfer VA Bank</strong>, atau <strong>Tunai (COD/Kasir)</strong>.
                </p>
                <a href="#katalog-section" class="btn-buy" style="background: #fde047; color: #064e3b; font-weight: 800; padding: 12px 24px; border-radius: 12px;">
                    Lihat Pilihan Beras
                </a>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <div class="filter-section" id="katalog-section">
        <div class="filter-box">
            <!-- Filter Kategori -->
            <div class="filter-group">
                <span class="filter-label">Varietas:</span>
                <a href="{{ route('home') }}" class="filter-btn {{ !request('kategori') ? 'active' : '' }}">Semua</a>
                @foreach($kategoris as $kat)
                    <a href="{{ route('home', array_merge(request()->query(), ['kategori' => $kat->id_kategori])) }}" 
                       class="filter-btn {{ request('kategori') == $kat->id_kategori ? 'active' : '' }}">
                        {{ $kat->nama_kategori }}
                    </a>
                @endforeach
            </div>

            <!-- Filter Kualitas Mutu -->
            <div class="filter-group">
                <span class="filter-label">Kualitas:</span>
                <a href="{{ route('home', request()->except('kualitas')) }}" class="filter-btn {{ !request('kualitas') ? 'active' : '' }}">Semua Grade</a>
                @foreach($kualitass as $mutu)
                    <a href="{{ route('home', array_merge(request()->query(), ['kualitas' => $mutu->id_kualitas])) }}" 
                       class="filter-btn {{ request('kualitas') == $mutu->id_kualitas ? 'active' : '' }}">
                        {{ $mutu->nama_kualitas }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Catalog Grid -->
    <section class="catalog-section">
        <div class="section-header">
            <h2>Katalog Beras Organik Berasera</h2>
            <p>Pilih varietas dan tingkatan mutu beras organik sesuai kebutuhan konsumsi keluarga sehat Anda.</p>
        </div>

        <div class="products-grid">
            @forelse($produks as $p)
                <div class="product-card">
                    <div class="product-img-wrap">
                        @if($p->id_kategori == 1)
                            🌾
                        @elseif($p->id_kategori == 2)
                            🍙
                        @else
                            🍚
                        @endif
                        <span class="grade-tag">{{ $p->kualitas->nama_kualitas ?? 'Grade A' }}</span>
                    </div>

                    <div class="product-body">
                        <span class="product-cat">{{ $p->kategori->nama_kategori ?? 'Organik' }}</span>
                        <h3 class="product-title">{{ $p->nama_produk }}</h3>
                        <p class="product-desc">{{ $p->deskripsi ?? 'Beras organik alami tanpa pengawet.' }}</p>

                        <div class="product-specs">
                            <div>✓ Kemasan: <strong>{{ $p->kualitas->keterangan_kemasan ?? 'Vakum' }}</strong></div>
                            <div>✓ Standar Patah: <strong>{{ $p->kualitas->standar_butir_patah ?? '<= 5%' }}</strong></div>
                            <div>✓ Kadar Air Maks: <strong>{{ $p->kualitas->kadar_air_maks ?? '14.0' }}%</strong></div>
                        </div>

                        <div class="product-footer">
                            <div>
                                <div class="product-price">
                                    Rp {{ number_format($p->harga, 0, ',', '.') }}
                                    <small>/ {{ $p->satuan }}</small>
                                </div>
                                <div class="stock-indicator">
                                    Stok: {{ $p->stok }} {{ $p->satuan }}
                                </div>
                            </div>

                            <form action="{{ route('cart.add', $p->id_produk) }}" method="POST">
                                @csrf
                                <input type="hidden" name="qty" value="1">
                                <button type="submit" class="btn-buy" {{ $p->stok <= 0 ? 'disabled' : '' }}>
                                    🛒 Beli
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px;">
                    <p style="font-size: 16px; color: #64748b;">Belum ada produk beras pada kategori ini.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- FIFO System Info Banner -->
    <div class="info-banner" id="fifo-section" style="scroll-margin-top: 100px;">
        <div class="fifo-box">
            <div>
                <h3>📦 Penjaminan Kesegaran Sistem FIFO (First In, First Out)</h3>
                <p>
                    Setiap karung beras hasil giling petani Poktan Gapsera Sejahtera Mandiri diberi nomor batch dan tanggal masuk gudang. 
                    Saat pembeli melakukan pesanan, sistem cerdas kami secara otomatis mengambil persediaan dari <strong>batch terawal</strong>. 
                    Dengan begitu, beras yang Anda konsumsi senantiasa segar, aromanya terjaga, dan bebas risiko apek.
                </p>
            </div>
            <div class="feature-list-box" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 14px; padding: 22px;">
                <h4 style="color: #fde047; margin-bottom: 12px; font-size: 15px; font-weight: 700;">Keunggulan Berasera:</h4>
                <ul style="list-style: none; font-size: 13px; color: #e2e8f0; line-height: 1.8;">
                    <li>✓ 100% Organik sertifikasi bebas pestisida kimia</li>
                    <li>✓ Kemasan vakum higienis menjaga kelembaban prima</li>
                    <li>✓ Reward Poin Belanja yang bisa dipakai untuk potongan harga</li>
                    <li>✓ Pembayaran instan Midtrans QRIS, Bank VA, atau Tunai</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Standar Mutu Beras Section (Berdasarkan Tabel tb_kualitas) -->
    <section class="catalog-section" id="kualitas-section" style="margin-top: 60px; scroll-margin-top: 100px;">
        <div class="section-header" style="text-align: center; max-width: 720px; margin: 0 auto 36px auto;">
            <h2>✨ Standar Mutu Beras Organik Berasera</h2>
            <p>Parameter standarisasi mutu beras perkumpulan Poktan Gapsera Sejahtera Mandiri sesuai SNI dan database mutu beras.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
            @foreach($kualitass as $mutu)
                <div style="background: white; border-radius: 18px; border: 1px solid #e2e8f0; padding: 26px; box-shadow: 0 4px 16px rgba(0,0,0,0.03); display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 5px; background: {{ $mutu->id_kualitas == 1 ? '#059669' : ($mutu->id_kualitas == 2 ? '#d97706' : '#64748b') }};"></div>
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px;">
                            <span style="font-size: 26px;">{{ $mutu->id_kualitas == 1 ? '🥇' : ($mutu->id_kualitas == 2 ? '🥈' : '🥉') }}</span>
                            <span style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 20px; background: {{ $mutu->id_kualitas == 1 ? '#ecfdf5' : '#fef3c7' }}; color: {{ $mutu->id_kualitas == 1 ? '#047857' : '#b45309' }};">
                                Grade #{{ $mutu->id_kualitas }}
                            </span>
                        </div>
                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">{{ $mutu->nama_kualitas }}</h3>
                        <p style="font-size: 13px; color: #64748b; margin-bottom: 20px; line-height: 1.5;">
                            Standar pengemasan: <strong>{{ $mutu->keterangan_kemasan ?? 'Standar Higienis' }}</strong>
                        </p>

                        <div style="background: #f8fafc; border-radius: 12px; padding: 14px; font-size: 12.5px; display: flex; flex-direction: column; gap: 8px; border: 1px solid #f1f5f9;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Maks. Butir Patah:</span>
                                <strong style="color: #0f172a;">{{ $mutu->standar_butir_patah }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Maks. Kadar Air:</span>
                                <strong style="color: #0f172a;">{{ $mutu->kadar_air_maks }}%</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">Min. Derajat Sosoh:</span>
                                <strong style="color: #0f172a;">{{ $mutu->derajat_sosoh_min ?? 90 }}%</strong>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 22px;">
                        <a href="{{ route('home', ['kualitas' => $mutu->id_kualitas]) }}#katalog-section" 
                           class="btn-buy" 
                           style="width: 100%; justify-content: center; text-decoration: none; font-size: 13px; padding: 10px; background: {{ $mutu->id_kualitas == 1 ? '#059669' : '#334155' }};">
                            Lihat Beras {{ $mutu->nama_kualitas }} →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

@endsection
