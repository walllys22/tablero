<x-app-layout>
    <x-slot name="header">
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Panel principal del sistema de torneos.</p>
    </x-slot>

    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('people.browse') }}" class="dashboard-card">
                    <i class="bi bi-people"></i>
                    <span>Personas</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('tablero.kumite') }}" class="dashboard-card">
                    <i class="bi bi-stopwatch"></i>
                    <span>Tablero Kumite</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('tablero.kata') }}" class="dashboard-card">
                    <i class="bi bi-qr-code"></i>
                    <span>Tablero Kata</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
