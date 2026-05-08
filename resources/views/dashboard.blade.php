<x-app-layout>
    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('people.browse') }}" class="dashboard-card">
                    <img src="{{ asset('images/campeonato.png') }}" alt="Personas">
                    <span>Personas</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('tablero.kumite') }}" class="dashboard-card">
                    <img src="{{ asset('images/kumite.png') }}" alt="Tablero Kumite">
                    <span>Tablero Kumite</span>
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('tablero.kata') }}" class="dashboard-card">
                    <img src="{{ asset('images/kata.png') }}" alt="Tablero Kata">
                    <span>Tablero Kata</span>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
