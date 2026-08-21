<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SDM Villa Merah')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }

        .main-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 260px;
            background: #101827;
            color: white;
            overflow-y: auto;
            position: fixed;
            height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #1f2937;
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 12px;
            color: #9ca3af;
        }

        .sidebar-logo { width: 58px; height: 58px; object-fit: contain; margin: 0 auto 8px; display: block; }

        .sidebar-menu {
            flex: 1;
            padding: 15px 0;
            overflow-y: auto;
        }
        .sidebar-date { margin: 12px 22px 20px; padding: 10px 12px; border: 1px solid rgba(148,163,184,.2); border-radius: 10px; color: #cbd5e1; font-size: .82rem; display: flex; gap: 9px; align-items: center; }
        .sidebar-date i { color: #93c5fd; font-size: 1rem; }

        .menu-item {
            padding: 12px 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .menu-item:hover {
            background-color: #1f2937;
            border-left-color: #e63946;
        }

        .menu-item.active {
            background-color: #1d2b43;
            border-left-color: #e63946;
            font-weight: 600;
        }

        .menu-item a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .menu-item a:hover {
            color: #ffffff;
        }

        .menu-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #9ec5ff;
            background: rgba(56, 189, 248, 0.1);
            font-size: 16px;
        }

        .menu-item.active .menu-icon,
        .menu-item:hover .menu-icon {
            color: #ffffff;
            background: rgba(230, 57, 70, 0.28);
        }

        .submenu {
            display: none;
            background-color: #0f172a;
        }

        .submenu.show {
            display: block;
        }

        .submenu-item {
            padding: 8px 20px 8px 50px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submenu-item:hover {
            background-color: #1f2937;
            padding-left: 55px;
        }

        .submenu-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .submenu-item a:hover {
            color: #ffffff;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            overflow-y: auto;
        }

        .content-wrapper {
            padding: 30px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .header-section h1 {
            color: #333;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 6px;
        }

        .logout-top-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            line-height: 1.2;
        }

        .logout-top-btn:hover {
            background-color: #c82333;
        }

        .back-dashboard-btn {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            margin-bottom: 18px;
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #111827;
            background: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }

        .back-dashboard-btn:hover {
            border-color: #38bdf8;
            color: #0f172a;
            background: #f8fafc;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                z-index: 1000;
                transition: width 0.3s ease;
            }

            .sidebar.mobile-open {
                width: 260px;
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 15px;
            }
        }

        /* Scrollbar styling */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="page-loader" aria-live="polite" aria-label="Memuat halaman">
        <div class="page-loader-inner"><span class="page-loader-spinner" aria-hidden="true"></span><span>Memuat halaman...</span></div>
    </div>
    <div id="route-progress" aria-hidden="true"></div>
    <div class="main-container">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-wrapper">
                <div class="header-section">
                    <h1>@yield('page_title', 'Dashboard')</h1>
                    <div class="user-info">
                        <div class="user-actions">
                            @if (session('impersonator_id'))
                                <form method="POST" action="{{ route('impersonation.stop') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm">Kembali ke Admin</button>
                                </form>
                            @endif
                            <span>{{ auth()->user()->name ?? 'User' }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-top-btn">Keluar</button>
                            </form>
                        </div>
                        <div class="user-avatar">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                </div>

                @unless (request()->routeIs('dashboard') || request()->routeIs(auth()->user()?->homeRoute()))
                    <a href="{{ route(auth()->user()?->homeRoute() ?? 'dashboard') }}" class="back-dashboard-btn">Kembali ke Halaman Utama</a>
                @endunless

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        // Toggle submenu
        document.querySelectorAll('.submenu-toggle').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const submenu = this.closest('.menu-item').nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    submenu.classList.toggle('show');
                }
            });
        });

        // Set active menu item
        document.querySelectorAll('.menu-item a').forEach(link => {
            if (link.href === window.location.href) {
                link.closest('.menu-item').classList.add('active');
            }
        });

        // Mobile menu toggle
        function toggleMobileSidebar() {
            document.querySelector('.sidebar').classList.toggle('mobile-open');
        }
    </script>
    @stack('scripts')
</body>
</html>
