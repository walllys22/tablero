@php
    $items = [
        ['label' => 'Menu Principal', 'icon' => '', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        
        // Opcion Administracion con submenu para Torneos, Modalidades y Personas
        [
            'label' => 'Administracion',
            'icon' => 'fa-solid fa-file-lines',
            'active' => request()->routeIs('torneos.*') || request()->routeIs('modalidades.*') || request()->routeIs('organizaciones.*') || request()->routeIs('licencias.*'),
            'children' => [
                ['label' => 'Torneos', 'icon' => 'fa-solid fa-trophy', 'route' => 'torneos.index', 'active' => request()->routeIs('torneos.*')],
                ['label' => 'Modalidades', 'icon' => 'fa-brands fa-markdown', 'modal' => 'modal-modalidades-sidebar', 'active' => request()->routeIs('modalidades.*')],
                ['label' => 'Listado Katas', 'icon' => 'fa-solid fa-clipboard-list', 'route' => 'dashboard', 'active' => false],
                ['label' => 'Personas', 'icon' => 'fa-solid fa-people-group', 'route' => 'people.browse', 'active' => request()->routeIs('people.*')],
                ['label' => 'Organizacion', 'icon' => 'fa-solid fa-torii-gate', 'route' => 'organizaciones.index', 'active' => request()->routeIs('organizaciones.*')],
                ['label' => 'Licencias', 'icon' => 'fa-solid fa-award', 'route' => 'licencias.index', 'active' => request()->routeIs('licencias.*')],
            ],
        ],

        // Opcion Registro con submenu para Organizacion y Competidores
        [
            'label' => 'Registro',
            'icon' => 'fa-solid fa-address-card',
            'active' => request()->is('registro*') || request()->routeIs('inscripciones.*') || request()->routeIs('arbitros.*'),
            'children' => [
                ['label' => 'Jueces', 'icon' => 'fa-solid fa-user-tie', 'modal' => 'modal-arbitros-sidebar', 'active' => request()->routeIs('arbitros.*')],
                ['label' => 'Inscripciones', 'icon' => 'fa-solid fa-address-book', 'modal' => 'modal-inscripciones-sidebar', 'active' => request()->routeIs('inscripciones.*')],
                ['label' => 'Competidores', 'icon' => 'fa-solid fa-image-portrait', 'route' => 'dashboard', 'active' => false],
            ],
        ],

        // Opcion Eventos con submenu para Tablero Kumite y Tablero Kata
        [
            'label' => 'Eventos',
            'icon' => 'fa-solid fa-calendar-days',
            'active' => request()->routeIs('tablero.*'),
            'children' => [
                ['label' => 'Tablero Kumite', 'icon' => 'fa-solid fa-keyboard', 'route' => 'tablero.kumite', 'active' => request()->routeIs('tablero.kumite')],
                ['label' => 'Tablero Kata', 'icon' => 'fa-solid fa-chalkboard', 'route' => 'tablero.kata', 'active' => request()->routeIs('tablero.kata')],
            ],
        ],

        // Opcion Usuario con submenu para Roles y Usuarios
        [
            'label' => 'Usuario',
            'icon' => 'fa-solid fa-user-gear',
            'active' => request()->is('usuarios*') || request()->is('roles*'),
            'children' => [
                ['label' => 'Roles', 'icon' => 'fa-solid fa-user-shield', 'route' => 'dashboard', 'active' => false],
                ['label' => 'Usuarios', 'icon' => 'fa-solid fa-users', 'route' => 'dashboard', 'active' => false],
            ],
        ],

        ['label' => 'Limpiar Cache', 'icon' => 'bi-brush', 'route' => 'cache.clear', 'method' => 'POST', 'active' => false],
    ];
@endphp

<aside class="app-sidebar d-none d-md-flex" style="margin-right: -20px;">
    <div class="sidebar-section">
        <nav class="sidebar-nav">
            @foreach ($items as $item)
                @if (isset($item['children']))
                    @php
                        $isActive = $item['active'] ?? false;
                    @endphp
                    <button class="sidebar-link sidebar-toggle {{ $isActive ? 'active' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-{{ Str::slug($item['label']) }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="sidebar-{{ Str::slug($item['label']) }}">
                        <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </button>
                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="sidebar-{{ Str::slug($item['label']) }}">
                        <div class="sidebar-subnav">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childActive = $child['active'] ?? false;
                                @endphp
                                @if (isset($child['modal']))
                                    <button type="button" class="sidebar-link sidebar-sublink {{ $childActive ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#{{ $child['modal'] }}">
                                        <i class="{{ str_starts_with($child['icon'], 'fa-') ? $child['icon'] : 'bi ' . $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </button>
                                @else
                                    <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $childActive ? 'active' : '' }}">
                                        <i class="{{ str_starts_with($child['icon'], 'fa-') ? $child['icon'] : 'bi ' . $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    @if (($item['method'] ?? 'GET') === 'POST')
                        <form method="POST" action="{{ route($item['route']) }}" class="m-0">
                            @csrf
                            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start {{ ($item['active'] ?? false) ? 'active' : '' }}">
                                <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route($item['route']) }}" class="sidebar-link {{ ($item['active'] ?? false) ? 'active' : '' }}">
                            <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
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
                    @php
                        $isActive = $item['active'] ?? false;
                    @endphp
                    <button class="sidebar-link sidebar-toggle {{ $isActive ? 'active' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#mobile-sidebar-{{ Str::slug($item['label']) }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}" aria-controls="mobile-sidebar-{{ Str::slug($item['label']) }}">
                        <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
                    </button>
                    <div class="collapse {{ $isActive ? 'show' : '' }}" id="mobile-sidebar-{{ Str::slug($item['label']) }}">
                        <div class="sidebar-subnav">
                            @foreach ($item['children'] as $child)
                                @php
                                    $childActive = $child['active'] ?? false;
                                @endphp
                                @if (isset($child['modal']))
                                    <button type="button" class="sidebar-link sidebar-sublink {{ $childActive ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#{{ $child['modal'] }}">
                                        <i class="{{ str_starts_with($child['icon'], 'fa-') ? $child['icon'] : 'bi ' . $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </button>
                                @else
                                    <a href="{{ route($child['route']) }}" class="sidebar-link sidebar-sublink {{ $childActive ? 'active' : '' }}">
                                        <i class="{{ str_starts_with($child['icon'], 'fa-') ? $child['icon'] : 'bi ' . $child['icon'] }}"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    @if (($item['method'] ?? 'GET') === 'POST')
                        <form method="POST" action="{{ route($item['route']) }}" class="m-0">
                            @csrf
                            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start {{ ($item['active'] ?? false) ? 'active' : '' }}">
                                <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route($item['route']) }}" class="sidebar-link {{ ($item['active'] ?? false) ? 'active' : '' }}">
                            <i class="{{ str_starts_with($item['icon'], 'fa-') ? $item['icon'] : 'bi ' . $item['icon'] }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
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
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalModalidadesSidebarLabel">Seleccionar campeonato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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
                    <button type="submit" class="btn btn-success" {{ $torneosModalidades->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-box-arrow-in-right"></i> Abrir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-inscripciones-sidebar" tabindex="-1" aria-labelledby="modalInscripcionesSidebarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-inscripciones-sidebar">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalInscripcionesSidebarLabel">Seleccionar campeonato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label for="torneo_inscripcion_sidebar" class="form-label">Campeonato</label>
                    <select id="torneo_inscripcion_sidebar" class="form-select" required>
                        <option value="">Seleccione un campeonato</option>
                        @foreach ($torneosModalidades as $torneo)
                            <option value="{{ $torneo->id }}">{{ $torneo->nombre ?: 'Torneo #' . $torneo->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" {{ $torneosModalidades->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-box-arrow-in-right"></i> Abrir
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-arbitros-sidebar" tabindex="-1" aria-labelledby="modalArbitrosSidebarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-arbitros-sidebar">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="modalArbitrosSidebarLabel">Seleccionar campeonato</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <label for="torneo_arbitro_sidebar" class="form-label">Campeonato</label>
                    <select id="torneo_arbitro_sidebar" class="form-select" required>
                        <option value="">Seleccione un campeonato</option>
                        @foreach ($torneosModalidades as $torneo)
                            <option value="{{ $torneo->id }}">{{ $torneo->nombre ?: 'Torneo #' . $torneo->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" {{ $torneosModalidades->isEmpty() ? 'disabled' : '' }}>
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

    document.getElementById('form-inscripciones-sidebar').addEventListener('submit', function (event) {
        event.preventDefault();

        let torneoId = document.getElementById('torneo_inscripcion_sidebar').value;
        if (!torneoId) {
            return;
        }

        window.location.href = `{{ url('/torneos') }}/${torneoId}/inscripciones`;
    });

    document.getElementById('form-arbitros-sidebar').addEventListener('submit', function (event) {
        event.preventDefault();

        let torneoId = document.getElementById('torneo_arbitro_sidebar').value;
        if (!torneoId) {
            return;
        }

        window.location.href = `{{ url('/torneos') }}/${torneoId}/arbitros`;
    });
</script>
