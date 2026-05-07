@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Resumen general del sistema')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h2 class="h4 mb-3">Bienvenido a Kaiteki</h2>
                    <p class="text-muted">Aquí puedes ver un resumen rápido de tus registros, competiciones y accesos principales.</p>
                    <div class="row g-3 mt-4">
                        <div class="col-sm-4">
                            <div class="p-4 rounded-3 border bg-white h-100">
                                <div class="text-uppercase text-muted mb-2">Personas</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="mb-1">128</h3>
                                        <small class="text-muted">Registros totales</small>
                                    </div>
                                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-4 rounded-3 border bg-white h-100">
                                <div class="text-uppercase text-muted mb-2">Kumite</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="mb-1">24</h3>
                                        <small class="text-muted">Combates activos</small>
                                    </div>
                                    <i class="bi bi-lightning-charge-fill fs-3 text-warning"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-4 rounded-3 border bg-white h-100">
                                <div class="text-uppercase text-muted mb-2">Kata</div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h3 class="mb-1">12</h3>
                                        <small class="text-muted">Torneos programados</small>
                                    </div>
                                    <i class="bi bi-award-fill fs-3 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h3 class="h5 mb-3">Accesos rápidos</h3>
                    <div class="d-grid gap-3">
                        <a href="{{ route('people.browse') }}" class="btn btn-primary">Personas</a>
                        <a href="{{ route('tablero.kumite') }}" class="btn btn-primary">Kumite</a>
                        <a href="{{ route('tablero.kata') }}" class="btn btn-primary">Kata</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
