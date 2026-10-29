<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Berasera - Penjualan Beras Organik Gapsera Sejahtera Mandiri')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #064e3b;
            --primary-hover: #047857;
            --primary-light: #ecfdf5;
            --accent: #d97706;
            --accent-light: #fef3c7;
            --dark: #0f172a;
            --dark-card: #1e293b;
            --slate-light: #f8fafc;
            --slate-border: #e2e8f0;
            --text-dark: #0f172a;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: var(--text-dark);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Announcement Bar */
        .top-bar {
            background: linear-gradient(90deg, #064e3b 0%, #047857 100%);
            color: #ecfdf5;
            font-size: 12px;
            font-weight: 500;
            padding: 8px 24px;
        }

        .top-bar-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .top-bar strong {
            color: #fef08a;
        }

        /* Main Navbar */
        .navbar-wrapper {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--slate-border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
        }

        .navbar {
            max-width: 1280px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        .brand-text h1 {
            font-size: 20px;
            font-weight: 800;
            color: #064e3b;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        .brand-text span {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        /* Nav Menu Links */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            list-style: none;
        }

        .nav-link {
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            font-size: 13.5px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover {
            color: var(--primary);
            background: #f1f5f9;
        }

        /* Nav Right / Actions */
        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .cart-btn {
            position: relative;
            background: #f1f5f9;
            color: #1e293b;
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .cart-btn:hover {
            background: #e2e8f0;
            border-color: #cbd5e1;
        }

        .cart-badge {
            background: #ef4444;
            color: white;
            font-size: 11px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 20px;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
        }

        /* User Dropdown Pill */
        .user-menu-pill {
            display: inline-flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 4px 6px 4px 10px;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            position: relative;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.2;
        }

        .user-info-text .name {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .points-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid #fde68a;
        }

        .btn-history {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 7px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
            transition: all 0.2s;
        }

        .btn-history:hover {
            background: #d1fae5;
            border-color: #6ee7b7;
        }

        .btn-logout {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background: #fee2e2;
        }

        .btn-auth {
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-login {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25);
        }

        .btn-login:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-admin-link {
            background: transparent;
            color: #64748b;
            font-size: 12px;
            padding: 7px 10px;
            border-radius: 8px;
        }

        .btn-admin-link:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        /* Mobile Hamburger */
        .mobile-toggle {
            display: none;
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 18px;
            cursor: pointer;
            color: #334155;
        }

        /* Flash Message Alert */
        .alert {
            padding: 14px 20px;
            margin: 16px auto;
            max-width: 1280px;
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* Main Content */
        .main-content {
            flex: 1;
        }

        /* Footer */
        .footer {
            background: #09131f;
            color: #94a3b8;
            padding: 50px 24px 24px 24px;
            margin-top: 60px;
            border-top: 1px solid #1e293b;
        }

        .footer-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h3 {
            color: white;
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .footer-col p {
            font-size: 13px;
            line-height: 1.7;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 8px;
            font-size: 13px;
        }

        .footer-col ul li a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-col ul li a:hover {
            color: #34d399;
        }

        .footer-bottom {
            max-width: 1280px;
            margin: 0 auto;
            padding-top: 20px;
            border-top: 1px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            flex-wrap: wrap;
            gap: 12px;
        }

        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }
            .mobile-toggle {
                display: block;
            }
        }

        @media (max-width: 768px) {
            .top-bar-inner {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
            .navbar {
                flex-wrap: wrap;
            }
            .nav-right {
                width: 100%;
                justify-content: space-between;
                flex-wrap: wrap;
                margin-top: 8px;
                padding-top: 8px;
                border-top: 1px dashed #e2e8f0;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Top Announcement -->
    <div class="top-bar">
        <div class="top-bar-inner">
            <div>
                🌾 <strong>Berasera:</strong> Beras Organik Sehat Langsung dari Petani Poktan Gapsera Sejahtera Mandiri, Kota Metro
            </div>
            <div>
                Standar Mutu <strong>FIFO</strong> & Pembayaran Digital <strong>Midtrans (QRIS/VA) / Tunai</strong>
            </div>
        </div>
    </div>

    <!-- Main Sticky Navbar -->
    <header class="navbar-wrapper">
        <div class="navbar">
            <!-- Brand -->
            <a href="{{ route('home') }}" class="brand">
                <div class="brand-logo">🌾</div>
                <div class="brand-text">
                    <h1>BERASERA</h1>
                    <span>Gapsera Sejahtera Mandiri</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <ul class="nav-menu">
                <li><a href="{{ route('home') }}" class="nav-link">🌾 Katalog Beras</a></li>
                <li><a href="{{ route('home') }}#kualitas-section" class="nav-link">✨ Standar Mutu</a></li>
                <li><a href="{{ route('home') }}#fifo-section" class="nav-link">📦 Sistem FIFO</a></li>
            </ul>

            <!-- Nav Actions (Cart & User) -->
            <div class="nav-right">
                <!-- Keranjang -->
                @php $cartCount = count(session('cart', [])); @endphp
                <a href="{{ route('cart.index') }}" class="cart-btn" title="Lihat Keranjang Belanja">
                    <span>🛒</span>
                    <span>Keranjang</span>
                    @if($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>

                <!-- Status Login Pembeli -->
                @if(Auth::guard('customer')->check())
                    <a href="{{ route('orders.riwayat') }}" class="btn-history" title="Lihat Riwayat Pembelian">
                        📋 Riwayat Pesanan
                    </a>
                    <a href="{{ route('customer.profile') }}" class="user-menu-pill" style="text-decoration: none; color: inherit;" title="Buka Profil Saya">
                        <span class="points-badge" title="Poin Belanja Anda">
                            ★ {{ Auth::guard('customer')->user()->total_poin }} Poin
                        </span>
                        <div class="user-info-text">
                            <span class="name">👤 {{ Auth::guard('customer')->user()->nama_customer }}</span>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-logout" title="Keluar dari akun">Keluar</button>
                    </form>
                @elseif(Auth::guard('web')->check())
                    <div class="user-menu-pill">
                        <span style="font-size: 12px; font-weight: 700; color: #059669;">[Pengurus: {{ Auth::guard('web')->user()->nama }}]</span>
                        <a href="{{ route('admin.dashboard') }}" class="btn-auth btn-login" style="padding: 5px 12px; font-size: 12px;">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout" title="Keluar">Keluar</button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn-auth btn-login">Masuk / Daftar</a>
                    <a href="{{ route('admin.login') }}" class="btn-auth btn-admin-link">Login Pengurus</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="alert alert-success">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-success" style="background: #e0f2fe; color: #0369a1; border-color: #bae6fd;">
            <span>ℹ️</span>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    <!-- Main View Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <h3>SIM Penjualan Beras Organik Berasera</h3>
                <p>
                    Sistem informasi manajemen rantai pasok dan penjualan beras organik perkumpulan kelompok tani (Poktan) 
                    <strong>Gapsera Sejahtera Mandiri</strong>, Kota Metro. Dilengkapi penjaminan mutu kesegaran FIFO, 
                    pembukuan laba rugi riil, dan gateway pembayaran online terintegrasi Midtrans.
                </p>
            </div>
            <div class="footer-col">
                <h3>Varietas Beras Organik</h3>
                <ul>
                    <li><a href="{{ route('home', ['kategori' => 1]) }}">Beras Merah Organik</a></li>
                    <li><a href="{{ route('home', ['kategori' => 2]) }}">Beras Hitam Organik</a></li>
                    <li><a href="{{ route('home', ['kategori' => 3]) }}">Beras Mentik Wangi (Putih)</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Sekretariat & Hubungi</h3>
                <p>
                    Perkumpulan Poktan Gapsera Sejahtera Mandiri<br>
                    Kota Metro, Lampung<br>
                    Kontak Layanan: 0812-3456-7890
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <div>&copy; 2026 Perkumpulan Poktan Gapsera Sejahtera Mandiri. Mata Kuliah Basis Data Lanjut.</div>
            <div>Dikerjakan oleh <strong>Rangga Febriawan</strong></div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
