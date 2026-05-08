@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        [
            'label' => 'Eventos',
            'icon' => 'bi-calendar-event',
            'active' => request()->routeIs('torneos.*'),
            'children' => [
                ['label' => 'Torneos', 'icon' => 'bi-trophy', 'route' => 'torneos.index', 'active' => request()->routeIs('torneos.*')],
            ],
        ],
        ['label' => 'Personas', 'icon' => 'bi-people', 'route' => 'people.browse', 'active' => request()->routeIs('people.*')],
        ['label' => 'Tablero Kumite', 'icon' => 'bi-stopwatch', 'route' => 'tablero.kumite', 'active' => request()->routeIs('tablero.kumite')],
        ['label' => 'Tablero Kata', 'icon' => 'bi-qr-code', 'route' => 'tablero.kata', 'active' => request()->routeIs('tablero.kata')],
        ['label' => 'Perfil', 'icon' => 'bi-person-lines-fill', 'route' => 'profile.edit', 'active' => request()->routeIs('profile.*')],
    ];
@endphp

<aside class="app-sidebar d-none d-md-flex">
    <div class="sidebar-section">
        <span class="sidebar-label">Menu</span>
        <nav class="sidebar-nav">
            @foreach ($items as $item)
                @if (isset($item['children']))
                    <button class="sidebar-link sidebar-toggle {{ $item['active'] ? 'active' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-{{ Str::slug($item['label']) }}" aria-expanded="{{ $item['active'] ? 'true' : 'false' }}" aria-controls="sidebar-{{ Str::slug($item['label']) }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </button>
                    <div class="collapse {{ $item['active'] ? 'show' : '' }}" id="sidebar-{{ Str::slug($item['label']) }}">
                        <div class="sidebar-subnav">
                            @foreach ($item['children'] as $child)
                                <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}">
                                    <i class="bi {{ $child['icon'] }}"></i>
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

    @auth
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <strong>{{ Auth::user()->name }}</strong>
                <small>{{ Auth::user()->email }}</small>
            </div>
        </div>
    @endauth
</aside>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ config('app.name', 'Torneos') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <nav class="sidebar-nav">
            @foreach ($items as $item)
                @if (isset($item['children']))
                    <button class="sidebar-link sidebar-toggle {{ $item['active'] ? 'active' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-sidebar-{{ Str::slug($item['label']) }}" aria-expanded="{{ $item['active'] ? 'true' : 'false' }}" aria-controls="mobile-sidebar-{{ Str::slug($item['label']) }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </button>
                    <div class="collapse {{ $item['active'] ? 'show' : '' }}" id="mobile-sidebar-{{ Str::slug($item['label']) }}">
                        <div class="sidebar-subnav">
                            @foreach ($item['children'] as $child)
                                <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}">
                                    <i class="bi {{ $child['icon'] }}"></i>
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        @auth
            <hr>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Cerrar sesion
                </button>
            </form>
        @endauth
    </div>
</div>
