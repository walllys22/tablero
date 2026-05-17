<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm app-navbar">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-semibold" href="{{ route('dashboard') }}">
            <x-application-logo />
            <span>{{ config('app.name', 'Torneos') }}</span>
        </a>

        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Abrir menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="ms-auto d-none d-lg-flex align-items-center gap-3">
            @auth
                @php
                    $usuarioNav = Auth::user();
                    $imagenUsuarioNav = Auth::user()->imagen
                        ? asset('storage/' . ltrim(Auth::user()->imagen, '/'))
                        : null;
                    $rolUsuarioNav = $usuarioNav->roles->pluck('description')->filter()->first()
                        ?: $usuarioNav->roles->pluck('name')->filter()->first()
                        ?: 'Usuario';
                @endphp
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @if ($imagenUsuarioNav)
                            <img src="{{ $imagenUsuarioNav }}" alt="{{ $usuarioNav->name }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;" onerror="this.style.display='none';">
                        @else
                            <i class="bi bi-person-circle"></i>
                        @endif
                        <span class="d-inline-flex flex-column align-items-start lh-sm">
                            <span>{{ $rolUsuarioNav }}</span>
                            <small class="text-muted">{{ $usuarioNav->name }}</small>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-lines-fill me-2"></i> Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>
