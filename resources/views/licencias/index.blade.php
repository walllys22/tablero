@extends('layouts.app')

@section('title', 'Tipos de licencia')

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
                <h1 class="h3 mb-0"><i class="bi bi-award"></i> Tipos de licencia</h1>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create-licencia">
                    <i class="bi bi-plus-lg"></i> Crear
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                    <i class="bi bi-x-lg"></i> <span>Cerrar</span>
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th style="width: 140px; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($licencias as $licencia)
                                <tr>
                                    <td>{{ $licencia->nombre }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modal-edit-licencia-{{ $licencia->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form method="POST" action="{{ route('licencias.destroy', $licencia) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Seguro que desea eliminar este tipo de licencia?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No hay tipos de licencia registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-create-licencia" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('licencias.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Crear tipo de licencia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @error('nombre') is-invalid @enderror" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @foreach ($licencias as $licencia)
        <div class="modal fade" id="modal-edit-licencia-{{ $licencia->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('licencias.update', $licencia) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Editar tipo de licencia</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <label for="nombre_edit_{{ $licencia->id }}" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre_edit_{{ $licencia->id }}" value="{{ $licencia->nombre }}" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
