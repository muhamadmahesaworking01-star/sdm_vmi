@php
    $sidebarController = new \App\Http\Controllers\SidebarController();
    $menuItems = $sidebarController->getMenuItems();
    $userRole = auth()->user()->role ?? null;
    $roleNames = [
        'super_admin' => 'ADM - Super Admin',
        'direksi' => 'DRK - Direksi',
        'karyawan' => 'Karyawan',
        'pengajar' => 'PGJ - Pengajar',
        'karyawan_pengajar' => 'KPR - Double Role'
    ];
@endphp

<div class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <img src="{{ asset('images/logo-vm-bg.png') }}" alt="Logo Villa Merah" class="sidebar-logo">
        <h2>SDM Villa Merah</h2>
        <p>{{ $roleNames[$userRole] ?? 'User' }}</p>
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar-menu">
        @foreach ($menuItems as $item)
            @if (empty($item['children']))
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp
                <!-- Single menu item -->
                <div class="menu-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}">
                        <span class="menu-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </div>
            @else
                @php
                    $isParentActive = request()->routeIs($item['route']);
                    $isChildActive = collect($item['children'])->contains(fn ($child) => request()->routeIs($child['route']));
                @endphp
                <!-- Menu item with children -->
                <div class="menu-item {{ $isParentActive || $isChildActive ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}" class="submenu-toggle">
                        <span class="menu-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                        <span>{{ $item['title'] }}</span>
                    </a>
                </div>
                <div class="submenu {{ $isParentActive || $isChildActive ? 'show' : '' }}">
                    @foreach ($item['children'] as $child)
                        <div class="submenu-item">
                            <a href="{{ route($child['route']) }}">{{ $child['title'] }}</a>
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>

    <div class="sidebar-date" title="Hari ini">
        <i class="bi bi-calendar-date"></i>
        <span id="sidebar-today"></span>
    </div>
    <script>
        document.getElementById('sidebar-today').textContent = new Intl.DateTimeFormat('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' }).format(new Date());
    </script>

</div>
