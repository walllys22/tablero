@extends('layouts.app')

@section('title', 'Detalle de Persona')

@section('content')
    @php
        $person = $person ?? null;
    @endphp

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Detalles de la Persona</h1>
            <a href="{{ route('people.browse') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-list"></i> Volver
            </a>
        </div>
        <div class="card-body">
            @if ($person)
                <div class="row g-4">
                    <div class="col-md-3">
                        @if(!empty($person->image))
                            <img src="{{ asset('storage/' . $person->image) }}" class="img-fluid rounded border" alt="{{ $person->first_name }}">
                        @else
                            <div class="empty-photo">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>Documento</strong>
                                <p class="mb-0">{{ $person->ci ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Nombre completo</strong>
                                <p class="mb-0">{{ $person->first_name ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Fecha de nacimiento</strong>
                                <p class="mb-0">{{ optional($person->birth_date)->format('d/m/Y') ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Genero</strong>
                                <p class="mb-0">{{ $person->gender ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Email</strong>
                                <p class="mb-0">{{ $person->email ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Telefono</strong>
                                <p class="mb-0">{{ $person->phone ?: 'N/A' }}</p>
                            </div>
                            <div class="col-12">
                                <strong>Direccion</strong>
                                <p class="mb-0">{{ $person->address ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info mb-0">No se encontro informacion para mostrar.</div>
            @endif
        </div>
    </div>
@endsection
