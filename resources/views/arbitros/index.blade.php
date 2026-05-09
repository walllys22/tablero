@extends('layouts.app')

@section('title', 'Jueces')

@section('content')
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
                                <th>Cargo</th>
                                <th>Modalidad</th>
                                <th>Rango</th>
                                <th>Licencia</th>
                                <th style="width: 140px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($arbitros as $arbitro)
                                <tr>
                                    <td>{{ $arbitro->persona->first_name }}{{ $arbitro->persona->ci ? ' - CI ' . $arbitro->persona->ci : '' }}</td>
                                    <td>{{ $arbitro->cargo }}</td>
                                    <td>{{ $arbitro->modalidad }}</td>
                                    <td>{{ $arbitro->rango }}</td>
                                    <td>{{ $arbitro->licenciaTipo->nombre }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modal-edit-arbitro-{{ $arbitro->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('arbitros.destroy', [$torneo, $arbitro]) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Seguro que desea eliminar este juez?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay jueces registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('arbitros.partials.form', ['action' => route('arbitros.store', $torneo), 'method' => null, 'modalId' => 'modal-create-arbitro', 'title' => 'Crear juez', 'arbitro' => null])

    @foreach ($arbitros as $arbitro)
        @include('arbitros.partials.form', ['action' => route('arbitros.update', [$torneo, $arbitro]), 'method' => 'PATCH', 'modalId' => 'modal-edit-arbitro-' . $arbitro->id, 'title' => 'Editar juez', 'arbitro' => $arbitro])
    @endforeach
@endsection
