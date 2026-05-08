@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        [
            'label' => 'Eventos',
            'icon' => 'bi-calendar-event',
            'active' => request()->routeIs('torneos.*') || request()->routeIs('modalidades.*'),
            'children' => [
                ['label' => 'Torneos', 'icon' => 'bi-trophy', 'route' => 'torneos.index', 'active' => request()->routeIs('torneos.*')],
                ['label' => 'Modalidades', 'icon' => 'bi-list-check', 'modal' => 'modal-modalidades-sidebar', 'active' => request()->routeIs('modalidades.*')],
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
                                @if (isset($child['modal']))
                                    <button type="button" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#{{ $child['modal'] }}">
                                        <i class="bi {{ $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </button>
                                @else
                                    <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}">
                                        <i class="bi {{ $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endif
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
                                @if (isset($child['modal']))
                                    <button type="button" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#{{ $child['modal'] }}">
                                        <i class="bi {{ $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </button>
                                @else
                                    <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $child['active'] ? 'active' : '' }}">
                                        <i class="bi {{ $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endif
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

@php
    $torneosModalidades = \App\Models\Torneo::orderByDesc('id')->get();
@endphp

<div class="modal fade" id="modal-modalidades-sidebar" tabindex="-1" aria-labelledby="modalModalidadesSidebarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-modalidades-sidebar">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalModalidadesSidebarLabel">Seleccionar campeonato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label for="torneo_modalidad_sidebar" class="form-label">Campeonato</label>
                    <select id="torneo_modalidad_sidebar" class="form-select" required>
                        <option value="">Seleccione un campeonato</option>
                        @foreach ($torneosModalidades as $torneo)
                            <option value="{{ $torneo->id }}">{{ $torneo->nombre ?: 'Torneo #' . $torneo->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" {{ $torneosModalidades->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-box-arrow-in-right"></i> Abrir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('form-modalidades-sidebar').addEventListener('submit', function (event) {
        event.preventDefault();

        let torneoId = document.getElementById('torneo_modalidad_sidebar').value;
        if (!torneoId) {
            return;
        }

        window.location.href = `{{ url('/torneos') }}/${torneoId}/modalidades?return=dashboard`;
    });
</script>
