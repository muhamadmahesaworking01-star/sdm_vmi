<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div id="page-loader" aria-live="polite" aria-label="Memuat halaman">
        <div class="page-loader-inner"><span class="page-loader-spinner" aria-hidden="true"></span><span>Memuat halaman...</span></div>
    </div>
    <div id="route-progress" aria-hidden="true"></div>
    @php
        $sidebarController = new \App\Http\Controllers\SidebarController();
        $menuItems = $sidebarController->getMenuItems();
        $userRole = auth()->user()->role ?? null;
        $roleNames = [
            'super_admin' => 'ADM - Super Admin',
            'direksi' => 'DRK - Direksi',
            'karyawan' => 'Karyawan',
            'pengajar' => 'PGJ - Pengajar',
            'karyawan_pengajar' => 'KPR - Double Role',
        ];
    @endphp

    <div class="min-h-screen flex">
        <aside class="hidden md:flex md:w-72 md:flex-col bg-gray-900 text-white">
            <div class="px-6 py-5 border-b border-gray-800">
                <div class="text-xl font-semibold">SDM Villa Merah</div>
                <div class="mt-1 text-xs text-gray-400">{{ $roleNames[$userRole] ?? 'User' }}</div>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
                @foreach ($menuItems as $item)
                    @php
                        $isParentActive = request()->routeIs($item['route']);
                        $hasChildren = ! empty($item['children']);
                        $isChildActive = $hasChildren && collect($item['children'])->contains(fn ($child) => request()->routeIs($child['route']));
                    @endphp

                    <div>
                        <a href="{{ $hasChildren ? route($item['route']) : route($item['route']) }}"
                           class="flex items-center gap-3 rounded-md px-4 py-2 text-sm font-medium transition {{ $isParentActive || $isChildActive ? 'bg-indigo-600 text-white' : 'text-gray-200 hover:bg-gray-800 hover:text-white' }}">
                            <span class="w-8 shrink-0 text-xs text-gray-300">{{ $item['icon'] }}</span>
                            <span>{{ $item['title'] }}</span>
                        </a>

                        @if ($hasChildren)
                            <div class="mt-1 space-y-1 pl-11">
                                @foreach ($item['children'] as $child)
                                    <a href="{{ route($child['route']) }}"
                                       class="block rounded-md px-3 py-2 text-xs transition {{ request()->routeIs($child['route']) ? 'bg-gray-800 text-white' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                                        {{ $child['title'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Selamat datang</p>
                        <h1 class="text-lg font-semibold text-gray-800">Dashboard Admin</h1>
                    </div>

                    @auth
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->roleLabel() }}</p>
                                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="submit" class="rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">
                                        Keluar
                                    </button>
                                </form>
                            </div>

                            <p class="text-xs text-gray-500">Sistem Informasi SDM</p>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8">
                @unless (request()->routeIs('dashboard'))
                    <a href="{{ route('dashboard') }}" class="mb-5 inline-flex rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:border-sky-400 hover:bg-sky-50">
                        Kembali ke Dashboard Utama
                    </a>
                @endunless

                @yield('content')
            </main>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
