@extends('layouts.app')

@section('title', 'Jueces')

@section('content')
    <style>
        .juez-toggle {
            align-items: center;
            background: transparent;
            border: 0;
            color: #343a40;
            display: flex;
            font: inherit;
            font-weight: 700;
            gap: 7px;
            padding: 0;
            text-align: left;
            width: 100%;
        }

        .juez-toggle .bi-chevron-down {
            transition: transform .15s ease-in-out;
        }

        .juez-toggle.collapsed .bi-chevron-down {
            transform: rotate(-90deg);
        }

        .licencias-collapse {
            min-height: 0;
        }

        .licencia-item {
            border-bottom: 1px solid #e9ecef;
            min-height: 38px;
            padding: 6px 0;
        }

        .licencia-item:last-child {
            border-bottom: 0;
        }
    </style>

    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">Revise los datos del formulario.</div>
        @endif

        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0"><i class="bi bi-person-badge"></i> Jueces</h1>
                    <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create-arbitro">
                        <i class="bi bi-plus-lg"></i> Crear
                    </button>
                    <a href="{{ route('torneos.index') }}" class="btn btn-warning text-white">
                        <i class="bi bi-x-lg"></i> Cerrar
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th style="text-align: center;">Licencias</th>
                                <th style="width: 160px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jueces as $personaId => $licenciasJuez)
                                @php
                                    $primerArbitro = $licenciasJuez->first();
                                    $persona = $primerArbitro->persona;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <button
                                            type="button"
                                            class="juez-toggle collapsed"
                                            data-bs-toggle="collapse"
                                            data-bs-target=".licencias-juez-{{ $personaId }}"
                                            aria-expanded="false"
                                            aria-controls="licencias-juez-{{ $personaId }} acciones-juez-{{ $personaId }}"
                                        >
                                            <i class="bi bi-chevron-down"></i>
                                            <span>{{ $persona->first_name }}</span>
                                        </button>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <div class="collapse licencias-collapse licencias-juez-{{ $personaId }}" id="licencias-juez-{{ $personaId }}">
                                            @foreach ($licenciasJuez as $arbitro)
                                                <div class="licencia-item">
                                                    <strong>{{ $arbitro->licenciaTipo->nombre }}</strong>
                                                    <span class="text-muted">
                                                        - {{ $arbitro->cargo }} / {{ $arbitro->modalidad }} / Rango {{ $arbitro->rango }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-end" style="vertical-align: middle;">
                                        <div class="collapse licencias-collapse licencias-juez-{{ $personaId }}" id="acciones-juez-{{ $personaId }}">
                                            @foreach ($licenciasJuez as $arbitro)
                                                <div class="licencia-item d-flex justify-content-end gap-2">
                                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modal-edit-arbitro-{{ $arbitro->id }}" title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <form method="POST" action="{{ route('arbitros.destroy', [$torneo, $arbitro]) }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Seguro que desea eliminar esta licencia del juez?')" title="Eliminar">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay jueces registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    {{-- Botón Crear: Color verde (success) --}}
    @include('arbitros.partials.form', [
        'action' => route('arbitros.store', $torneo), 
        'method' => null, 
        'modalId' => 'modal-create-arbitro', 
        'title' => 'Crear juez', 
        'arbitro' => null,
        'color' => 'success' 
    ])

    {{-- Botón Editar: Color celeste (info) --}}
    @foreach ($arbitros as $arbitro)
        @include('arbitros.partials.form', [
            'action' => route('arbitros.update', [$torneo, $arbitro]), 
            'method' => 'PATCH', 
            'modalId' => 'modal-edit-arbitro-' . $arbitro->id, 
            'title' => 'Editar juez', 
            'arbitro' => $arbitro,
            'color' => 'info'
        ])
    @endforeach

{{-- 
    @include('arbitros.partials.form', ['action' => route('arbitros.store', $torneo), 'method' => null, 'modalId' => 'modal-create-arbitro', 'title' => 'Crear juez', 'arbitro' => null])

    @foreach ($arbitros as $arbitro)
        @include('arbitros.partials.form', ['action' => route('arbitros.update', [$torneo, $arbitro]), 'method' => 'PATCH', 'modalId' => 'modal-edit-arbitro-' . $arbitro->id, 'title' => 'Editar juez', 'arbitro' => $arbitro])
    @endforeach 
--}}
@endsection
