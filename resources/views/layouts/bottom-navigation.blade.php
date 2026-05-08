<nav class="bottom-bar d-md-none" aria-label="Navegacion inferior">
    <a href="{{ route('dashboard') }}" class="bottom-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i>
        <span>Inicio</span>
    </a>
    <a href="{{ route('people.browse') }}" class="bottom-link {{ request()->routeIs('people.*') ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span>Personas</span>
    </a>
    <a href="{{ route('tablero.kumite') }}" class="bottom-link {{ request()->routeIs('tablero.kumite') ? 'active' : '' }}">
        <i class="bi bi-stopwatch"></i>
        <span>Kumite</span>
    </a>
    <a href="{{ route('tablero.kata') }}" class="bottom-link {{ request()->routeIs('tablero.kata') ? 'active' : '' }}">
        <i class="bi bi-qr-code"></i>
        <span>Kata</span>
    </a>
</nav>
