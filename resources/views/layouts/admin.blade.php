<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Berasera SIM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #0f1b12;
            --sidebar-hover: #1a2e1e;
            --sidebar-active: #16a34a;
            --sidebar-text: #a7f3d0;
            --sidebar-muted: #6b7280;
            --sidebar-border: #1f2d24;
            --primary: #16a34a;
            --primary-light: #dcfce7;
            --accent: #d97706;
            --danger: #ef4444;
            --warning: #f59e0b;
            --surface: #ffffff;
            --bg: #f0fdf4;
            --border: #d1fae5;
            --text: #0f172a;
            --muted: #64748b;

            --sidebar-w-expanded: 260px;
            --sidebar-w-collapsed: 72px;
            --transition-speed: 0.25s;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* === SIDEBAR FLEKSIBEL (Bisa Expand / Collapse) === */
        .sidebar {
            width: var(--sidebar-w-expanded);
            min-height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid var(--sidebar-border);
            transition: width var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .sidebar-brand {
            padding: 20px 16px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .sidebar-brand .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            overflow: hidden;
            white-space: nowrap;
            flex: 1;
        }

        .sidebar-brand .logo-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 14px rgba(22,163,74,0.4);
        }

        .sidebar-brand .brand-title {
            display: flex;
            flex-direction: column;
            transition: opacity var(--transition-speed);
        }

        .sidebar-brand h2 {
            color: #ecfdf5;
            font-size: 17px;
            font-weight: 800;
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .sidebar-brand p {
            color: var(--sidebar-muted);
            font-size: 11px;
            margin-top: 1px;
        }

        /* Tombol Toggle Sidebar Fleksibel */
        .btn-toggle-sidebar {
            width: 32px;
            height: 32px;
            min-width: 32px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.05);
            color: #ecfdf5;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-toggle-sidebar:hover {
            background: rgba(22, 163, 74, 0.25);
            border-color: #16a34a;
            color: #4ade80;
        }

        .sidebar-user {
            padding: 16px;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 12px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.02);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #ffffff;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.3);
        }

        .user-info {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 3px;
            transition: opacity var(--transition-speed);
        }

        .user-info h4 {
            color: #ecfdf5;
            font-size: 13.5px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.25;
            margin: 0;
        }

        .user-info .role-badge {
            align-self: flex-start;
            font-size: 10.5px;
            padding: 2.5px 9px;
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.3px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .role-ketua {
            background: #fef08a;
            color: #713f12;
            border: 1px solid #fde047;
        }
        .role-bendahara {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .sidebar-nav {
            flex: 1;
            padding: 10px 8px;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--sidebar-muted);
            padding: 12px 14px 6px;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity var(--transition-speed);
        }

        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin-bottom: 2px;
            border-radius: 8px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            white-space: nowrap;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: #ecfdf5;
            transform: translateX(2px);
        }

        .nav-item.active {
            background: rgba(22,163,74,0.18);
            border-left-color: var(--sidebar-active);
            color: #4ade80;
            font-weight: 600;
        }

        .nav-item .icon {
            font-size: 16px;
            width: 22px;
            min-width: 22px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-item .nav-text {
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: opacity var(--transition-speed);
        }

        .nav-item .badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 10px;
            font-weight: 700;
        }

        /* Tooltip saat sidebar menciut (Collapsed) */
        .sidebar.collapsed .nav-item:hover::after {
            content: attr(data-tooltip);
            position: fixed;
            left: calc(var(--sidebar-w-collapsed) + 10px);
            background: #0f1b12;
            color: #ecfdf5;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            white-space: nowrap;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
            border: 1px solid var(--sidebar-border);
            z-index: 1000;
            pointer-events: none;
        }

        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 12px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 8px;
            color: #fca5a5;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .logout-btn:hover {
            background: rgba(239,68,68,0.2);
            color: #ef4444;
        }

        /* === STATUS COLLAPSED (HEMAT RUANG) === */
        .sidebar.collapsed {
            width: var(--sidebar-w-collapsed);
        }

        .sidebar.collapsed .brand-title,
        .sidebar.collapsed .user-info,
        .sidebar.collapsed .nav-section-title,
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .badge,
        .sidebar.collapsed .logout-btn span {
            display: none !important;
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .sidebar-brand {
            justify-content: center;
            padding: 16px 0;
        }

        .sidebar.collapsed .sidebar-brand .logo {
            justify-content: center;
            flex: initial;
        }

        .sidebar.collapsed .btn-toggle-sidebar {
            display: none;
        }

        .sidebar.collapsed .sidebar-user {
            justify-content: center;
            padding: 12px 0;
        }

        .sidebar.collapsed .nav-item {
            justify-content: center;
            padding: 12px 0;
            border-left: none;
            border-radius: 10px;
            margin: 2px 6px;
        }

        .sidebar.collapsed .logout-btn {
            justify-content: center;
            padding: 10px 0;
        }

        /* === MAIN CONTENT WRAPPER === */
        .main-wrapper {
            margin-left: var(--sidebar-w-expanded);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: var(--sidebar-w-collapsed);
        }

        /* Topbar Asli dengan Tombol Toggle Fleksibel */
        .topbar {
            background: white;
            border-bottom: 1px solid var(--border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .btn-topbar-toggle {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: #ffffff;
            color: var(--muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-topbar-toggle:hover {
            background: var(--bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .topbar-left h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
        }

        .topbar-left p {
            font-size: 12px;
            color: var(--muted);
            margin-top: 1px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-store-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .topbar-store-link:hover {
            background: #bbf7d0;
        }

        /* Alert flash */
        .flash-container {
            padding: 0 28px;
            margin-top: 16px;
        }

        .flash {
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .flash-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .flash-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .flash-info    { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .flash-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* Page content */
        .page-content {
            padding: 24px 28px 40px;
            flex: 1;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e8f5ee;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f0fdf4;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .card-body {
            padding: 22px;
        }

        /* Table */
        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f0fdf4;
            padding: 11px 14px;
            text-align: left;
            font-size: 12px;
            font-weight: 700;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 11px 14px;
            font-size: 13.5px;
            color: var(--text);
            border-bottom: 1px solid #f1fdf6;
            vertical-align: middle;
        }

        tr:hover td {
            background: #f9fefb;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary   { background: var(--primary); color: white; box-shadow: 0 3px 10px rgba(22,163,74,0.3); }
        .btn-primary:hover { background: #15803d; transform: translateY(-1px); }
        .btn-secondary { background: #f1f5f9; color: #334155; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger    { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        .btn-warning   { background: var(--warning); color: white; }
        .btn-warning:hover { background: #d97706; }
        .btn-sm        { padding: 5px 11px; font-size: 12px; }
        .btn-outline   { background: transparent; border: 1.5px solid var(--primary); color: var(--primary); }
        .btn-outline:hover { background: var(--primary-light); }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-success    { background: #dcfce7; color: #166534; }
        .badge-danger     { background: #fee2e2; color: #991b1b; }
        .badge-info       { background: #e0f2fe; color: #0369a1; }
        .badge-warning    { background: #fff7ed; color: #c2410c; }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background: #fff;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
        }

        select.form-control {
            cursor: pointer;
        }

        .form-row {
            display: grid;
            gap: 16px;
        }

        .form-row-2 { grid-template-columns: 1fr 1fr; }
        .form-row-3 { grid-template-columns: 1fr 1fr 1fr; }

        .invalid-feedback {
            color: var(--danger);
            font-size: 12px;
            margin-top: 4px;
        }

        /* Alert stok menipis */
        .stok-alert-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 18px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            font-size: 13px;
            color: #92400e;
            font-weight: 500;
            margin-bottom: 18px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 4px;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: var(--text);
            background: white;
            border: 1px solid var(--border);
        }

        .pagination .active span {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* Backdrop Mobile */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 27, 18, 0.5);
            backdrop-filter: blur(2px);
            z-index: 90;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-w-expanded) !important;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar Asli Berasera (Fleksibel & Responsif) -->
    <nav class="sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <div class="logo-icon">🌾</div>
                <div class="brand-title">
                    <h2>BERASERA</h2>
                    <p>Panel Manajemen</p>
                </div>
            </a>
            <!-- Tombol Toggle Mini di Sidebar Header -->
            <button type="button" class="btn-toggle-sidebar" onclick="toggleSidebar()" title="Kecilkan / Perbesar Menu">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        @php $user = Auth::guard('web')->user(); $role = $user->level ?? 'bendahara'; @endphp

        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr($user->nama ?? 'A', 0, 1)) }}</div>
            <div class="user-info">
                <h4>{{ $user->nama ?? 'Admin' }}</h4>
                <span class="role-badge role-{{ $role }}">{{ ucfirst($role) }}</span>
            </div>
        </div>

        <div class="sidebar-nav">
            <!-- Semua role -->
            <div class="nav-section-title">Umum</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
                <span class="icon">📊</span>
                <span class="nav-text">Dashboard</span>
            </a>

            @if($role === 'ketua')
            <!-- Ketua: Data Pengguna -->
            <div class="nav-section-title">Pengguna</div>
            <a href="{{ route('admin.users.pengurus') }}" class="nav-item {{ request()->routeIs('admin.users.pengurus*') ? 'active' : '' }}" data-tooltip="Data Pengurus">
                <span class="icon">👨‍💼</span>
                <span class="nav-text">Data Pengurus</span>
            </a>
            <a href="{{ route('admin.users.customer') }}" class="nav-item {{ request()->routeIs('admin.users.customer*') ? 'active' : '' }}" data-tooltip="Data Pembeli">
                <span class="icon">🧑‍🤝‍🧑</span>
                <span class="nav-text">Data Pembeli</span>
            </a>
            @endif

            <!-- Bendahara & Ketua: Pengelolaan -->
            <div class="nav-section-title">Pengelolaan</div>
            <a href="{{ route('admin.produk.index') }}" class="nav-item {{ request()->routeIs('admin.produk*') ? 'active' : '' }}" data-tooltip="Produk Beras">
                <span class="icon">🌾</span>
                <span class="nav-text">Produk Beras</span>
            </a>
            <a href="{{ route('admin.pemasok.index') }}" class="nav-item {{ request()->routeIs('admin.pemasok*') ? 'active' : '' }}" data-tooltip="Pemasok / Petani">
                <span class="icon">🚜</span>
                <span class="nav-text">Pemasok / Petani</span>
            </a>
            <a href="{{ route('admin.pasokan.index') }}" class="nav-item {{ request()->routeIs('admin.pasokan*') ? 'active' : '' }}" data-tooltip="Pembelian / Pasokan">
                <span class="icon">📦</span>
                <span class="nav-text">Pembelian / Pasokan</span>
            </a>

            <div class="nav-section-title">Penjualan</div>
            <a href="{{ route('admin.penjualan.index') }}" class="nav-item {{ request()->routeIs('admin.penjualan*') ? 'active' : '' }}" data-tooltip="Transaksi Penjualan">
                <span class="icon">🛒</span>
                <span class="nav-text">Transaksi Penjualan</span>
                @php $menunggu = \App\Models\Penjualan::where('status_penjualan','menunggu_pembayaran')->count(); @endphp
                @if($menunggu > 0)
                    <span class="badge">{{ $menunggu }}</span>
                @endif
            </a>

            <!-- Laporan -->
            <div class="nav-section-title">Laporan</div>
            <a href="{{ route('admin.laporan.penjualan') }}" class="nav-item {{ request()->routeIs('admin.laporan.penjualan*') ? 'active' : '' }}" data-tooltip="Laporan Penjualan">
                <span class="icon">📈</span>
                <span class="nav-text">Laporan Penjualan</span>
            </a>
            <a href="{{ route('admin.laporan.stok') }}" class="nav-item {{ request()->routeIs('admin.laporan.stok*') ? 'active' : '' }}" data-tooltip="Laporan Stok">
                <span class="icon">📋</span>
                <span class="nav-text">Laporan Stok</span>
            </a>
            <a href="{{ route('admin.laporan.pembelian') }}" class="nav-item {{ request()->routeIs('admin.laporan.pembelian*') ? 'active' : '' }}" data-tooltip="Laporan Pembelian">
                <span class="icon">🧾</span>
                <span class="nav-text">Laporan Pembelian</span>
            </a>
            <a href="{{ route('admin.laporan.laba-rugi') }}" class="nav-item {{ request()->routeIs('admin.laporan.laba-rugi*') ? 'active' : '' }}" data-tooltip="Laba Rugi FIFO">
                <span class="icon">💰</span>
                <span class="nav-text">Laba Rugi FIFO</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="{{ route('home') }}" class="logout-btn" style="margin-bottom:6px; color:#a7f3d0; border-color: rgba(167,243,208,0.2); background: rgba(167,243,208,0.05);">
                🏪 <span>Lihat Toko</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">🚪 <span>Keluar</span></button>
            </form>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper" id="mainWrapper">
        <!-- Topbar Asli dengan Tombol Toggle Fleksibel -->
        <div class="topbar">
            <div class="topbar-left">
                <button type="button" class="btn-topbar-toggle" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                    <p>@yield('page-subtitle', 'Selamat datang di Panel Manajemen Berasera')</p>
                </div>
            </div>
            <div class="topbar-right">
                @php $stokMenipis = \App\Models\Produk::where('stok', '<', 50)->where('stok', '>', 0)->count(); @endphp
                @if($stokMenipis > 0)
                    <a href="{{ route('admin.laporan.stok') }}" style="display:flex;align-items:center;gap:6px;padding:7px 12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;text-decoration:none;font-size:12.5px;font-weight:600;color:#92400e;">
                        ⚠️ {{ $stokMenipis }} Stok Menipis
                    </a>
                @endif
                <a href="{{ route('home') }}" class="topbar-store-link">🏪 Lihat Toko</a>
            </div>
        </div>

        <!-- Flash Messages -->
        <div class="flash-container">
            @if(session('success'))
                <div class="flash flash-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">⚠️ {{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="flash flash-info">ℹ️ {{ session('info') }}</div>
            @endif
            @if(session('warning'))
                <div class="flash flash-warning">🔔 {{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="flash flash-error">
                    ⚠️
                    <ul style="margin:0; padding-left:16px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    <!-- Script Fleksibilitas Sidebar (Expand/Collapse + State Persistence) -->
    <script>
        const sidebar = document.getElementById('adminSidebar');
        const mainWrapper = document.getElementById('mainWrapper');
        const backdrop = document.getElementById('sidebarBackdrop');

        function initSidebarState() {
            const isCollapsed = localStorage.getItem('berasera_sidebar_collapsed') === 'true';
            if (isCollapsed && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('sidebar-collapsed');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('mobile-open');
                backdrop.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('sidebar-collapsed');
                const nowCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('berasera_sidebar_collapsed', nowCollapsed);
            }
        }

        document.addEventListener('DOMContentLoaded', initSidebarState);
    </script>

    <!-- Berasera Audio Notifier -->
    <script src="{{ asset('js/berasera-audio.js') }}"></script>
    @if(session('audio_payment_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var audioData = @json(session('audio_payment_success'));
                if (audioData && audioData.nominal) {
                    bunyikanSuaraPembayaran(audioData.nominal, audioData.aktor || 'admin');
                }
            });
        </script>
    @endif

    @yield('scripts')
</body>
</html>
