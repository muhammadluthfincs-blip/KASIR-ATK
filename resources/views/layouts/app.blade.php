<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ATK POS</title>

    <!-- Favicon / Logo (Base64 Inline + Multi-size PNG + ICO) -->
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAB/UlEQVRYhc2X30tUQRTHP3O5f0Avm4tkESq4aWwZvfqyD6ZIIj4sSJjRX2GE0Uv/RfSD6CkJJZL+ACNaf22pPWQEKbHrvuQ/cKcHuTd359x7Z9250hcG5s4c5nPm3HOGGUWC+u83dNK8rb4/zam4OWOi717dCTROe8+6mphNH31ztUzhkRPP8xE36vTO/j4TeKgfL7tV5EDvnYPM4NX9BwAUe56YTry6oDwArYNMWggPHWmdB/AACLTz9rVUMHb85eBhsx2gLpd/Og//9uhQ1NcvpqP+1fxjw9ZDB7hsJ+EA6u7iMbzrkWjv68BdAHbGi+L4UG4hCnmrfILADXziujg++G4TiGf42oEDu7dviONXltcT4QA+ujMHdidvyvClSioccJsDoQpvPwN263aUA0drZZhqgS9+wmbnobzTnnJHa2UAuucvRYsNvPnY9jqq59aWGKtfK3JJAZzLDxpjf2o7qbu9OFY1xtqugtPCASSWr1OqQAK2axM6KLGcHURWEiNgWYaF1xupNt9mhhPnJVbHB1Fbkn6BbRKm7c6O/1/mgE7OAdsSs5HE8mqrI4ogwGhZqIVRWx1RPsj/JgvJOQCxtxXnEjgeQL1SUtKV2bVOrl+vlP49TEKdH/5wJqE43Bg1n2ahctdWMnWisTUW/zg1nCm+d+JMozoey/kLfXLT+o9Xsv4AAAAASUVORK5CYII=">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}?v={{ time() }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v={{ time() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}">

    <!-- Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- Custom Navy Dashboard Theme -->
    <style>
        :root {
            --atk-sidebar-bg: #0f172a;
            --atk-sidebar-hover: #1e293b;
            --atk-primary: #2563eb;
            --atk-primary-hover: #1d4ed8;
            --atk-body-bg: #f8fafc;
            --atk-card-bg: #ffffff;
            --atk-text-main: #1e293b;
            --atk-text-muted: #64748b;
            --atk-border: #e2e8f0;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--atk-body-bg);
            color: var(--atk-text-main);
            min-height: 100vh;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            width: 260px;
            background-color: var(--atk-sidebar-bg);
            color: #f1f5f9;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }

        .sidebar-brand {
            padding: 20px 24px;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar-brand .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        }

        .sidebar-menu {
            padding: 16px 12px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .menu-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            padding: 12px 14px 6px;
            font-weight: 600;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar-nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .sidebar-nav-item:hover {
            color: #ffffff;
            background-color: var(--atk-sidebar-hover);
        }

        .sidebar-nav-item:hover i {
            color: #38bdf8;
        }

        .sidebar-nav-item.active {
            color: #ffffff;
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .sidebar-nav-item.active i {
            color: #ffffff;
        }

        .sidebar-user-box {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: rgba(15, 23, 42, 0.6);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #3b82f6;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        /* Main Content Layout */
        #content-wrapper {
            margin-left: 260px;
            width: calc(100% - 260px);
            max-width: calc(100% - 260px);
            min-width: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* Desktop Collapsed Sidebar State */
        body.sidebar-collapsed #sidebar-wrapper {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed #content-wrapper {
            margin-left: 0;
            width: 100%;
            max-width: 100%;
        }

        .top-navbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--atk-border);
            padding: 12px clamp(14px, 2vw, 28px);
            position: sticky;
            top: 0;
            z-index: 1020;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        .main-container {
            padding: clamp(14px, 2vw, 24px);
            flex-grow: 1;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
        }

        /* Card Styles */
        .card {
            border: 1px solid var(--atk-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            background-color: #ffffff;
            max-width: 100%;
            min-width: 0;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--atk-border);
            padding: 14px 18px;
            font-weight: 600;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 12px;
            padding: 16px 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        /* Buttons & Badges */
        .btn-primary {
            background-color: var(--atk-primary);
            border-color: var(--atk-primary);
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
        }

        .btn-primary:hover {
            background-color: var(--atk-primary-hover);
            border-color: var(--atk-primary-hover);
        }

        .badge-soft-success {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-soft-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-soft-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Table Design */
        .table > :not(caption) > * > * {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                transform: translateX(-100%);
            }
            #sidebar-wrapper.show {
                transform: translateX(0);
            }
            #content-wrapper {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
            }
            .sidebar-backdrop {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 1030;
                display: none;
            }
            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <nav id="sidebar-wrapper">
        <a href="{{ auth()->user()->role === 'admin' ? route('dashboard') : route('pos.index') }}" class="sidebar-brand">
            <img src="{{ asset('favicon.svg') }}" alt="Logo" width="38" height="38" class="rounded-3 shadow-sm">
            <div>
                <div class="lh-1">ATK <span class="text-primary">POS</span></div>
                <small class="text-secondary fw-normal" style="font-size: 0.75rem;">Kasir & Stok ATK</small>
            </div>
        </a>

        <div class="sidebar-menu">
            @if(auth()->user()->role === 'admin')
            <div class="menu-header">Utama</div>
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            @endif

            <div class="menu-header">Kasir & Penjualan</div>
            <a href="{{ route('pos.index') }}" class="sidebar-nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calculator"></i>
                <span>Kasir POS</span>
                <span class="badge bg-primary ms-auto" style="font-size: 0.65rem;">POS</span>
            </a>
            <a href="{{ route('sales.index') }}" class="sidebar-nav-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Riwayat Penjualan</span>
            </a>

            @if(auth()->user()->role === 'admin')
            <div class="menu-header">Manajemen Master</div>
            <a href="{{ route('products.index') }}" class="sidebar-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Data Barang</span>
            </a>
            <a href="{{ route('categories.index') }}" class="sidebar-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>Kategori Barang</span>
            </a>
            <a href="{{ route('suppliers.index') }}" class="sidebar-nav-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck-field"></i>
                <span>Data Supplier</span>
            </a>

            <div class="menu-header">Inventori & Stok</div>
            <a href="{{ route('purchases.index') }}" class="sidebar-nav-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-flatbed"></i>
                <span>Barang Masuk (Beli)</span>
            </a>
            <a href="{{ route('stock-outs.index') }}" class="sidebar-nav-item {{ request()->routeIs('stock-outs.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box-archive"></i>
                <span>Barang Keluar</span>
            </a>
            <a href="{{ route('stocks.index') }}" class="sidebar-nav-item {{ request()->routeIs('stocks.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i>
                <span>Manajemen Stok</span>
            </a>

            <div class="menu-header">Laporan & Pengaturan</div>
            <a href="{{ route('reports.index') }}" class="sidebar-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Laporan & Keuntungan</span>
            </a>
            <a href="{{ route('users.index') }}" class="sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear"></i>
                <span>Pengguna Sistem</span>
            </a>
            @endif
        </div>

        <!-- Sidebar User Footer -->
        <div class="sidebar-user-box">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1 overflow-hidden">
                <div class="text-white text-truncate fw-semibold" style="font-size: 0.88rem;">{{ auth()->user()->name }}</div>
                <div class="text-secondary d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <span class="badge {{ auth()->user()->role === 'admin' ? 'bg-danger' : 'bg-primary' }}" style="font-size: 0.65rem;">
                        {{ strtoupper(auth()->user()->role) }}
                    </span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light border-0 text-secondary" title="Logout" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <div id="content-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0">
                <button class="btn btn-light border" id="sidebarToggle" type="button" title="Buka/Tutup Menu Sidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="d-none d-sm-block text-truncate">
                    <h5 class="mb-0 fw-bold text-dark text-truncate">@yield('page_title', 'ATK POS')</h5>
                    <small class="text-muted text-truncate d-block">@yield('page_subtitle', 'Sistem Kasir dan Stok Alat Tulis Kantor')</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 gap-md-3 ms-auto">
                <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2 shadow-sm text-nowrap">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="d-none d-md-inline">Buka Kasir POS</span>
                </a>

                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-circle-user text-primary"></i>
                        <span class="d-none d-sm-inline fw-medium text-truncate" style="max-width: 130px;">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </li>
                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-chart-pie me-2 text-primary"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('users.index') }}">
                                <i class="fa-solid fa-users me-2 text-secondary"></i> Kelola Pengguna
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Yakin ingin logout?')">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Flash Notifications -->
        <main class="main-container">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-xmark fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2 fw-semibold mb-1">
                    <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                    <span>Terdapat beberapa kesalahan input:</span>
                </div>
                <ul class="mb-0 ps-4">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto py-3 px-4 bg-white border-top text-center text-muted" style="font-size: 0.85rem;">
            <div>&copy; {{ date('Y') }} <strong>ATK POS</strong> - Sistem Kasir dan Manajemen Stok Toko Alat Tulis Kantor.</div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Responsive Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar-wrapper');
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');

            // Restore desktop sidebar collapsed state from localStorage
            if (window.innerWidth >= 992 && localStorage.getItem('atk_sidebar_collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    if (window.innerWidth < 992) {
                        sidebar.classList.toggle('show');
                        backdrop.classList.toggle('show');
                    } else {
                        document.body.classList.toggle('sidebar-collapsed');
                        localStorage.setItem('atk_sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? 'true' : 'false');
                    }
                });
            }

            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    sidebar.classList.remove('show');
                    backdrop.classList.remove('show');
                });
            }

            // Window resize handler
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992) {
                    if (backdrop) backdrop.classList.remove('show');
                    if (sidebar) sidebar.classList.remove('show');
                    if (localStorage.getItem('atk_sidebar_collapsed') === 'true') {
                        document.body.classList.add('sidebar-collapsed');
                    } else {
                        document.body.classList.remove('sidebar-collapsed');
                    }
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
            });
        });

        // Helper for formatting Rupiah in Javascript
        function formatRupiah(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
        }
    </script>

    @yield('scripts')
</body>
</html>

