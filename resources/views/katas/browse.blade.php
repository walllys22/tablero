@extends('layouts.app')

@section('title', 'Listado Katas')

@push('styles')
    <style>
        .katas-tabs {
            background: #e9e9e9;
            border: 0;
            padding: 0;
        }

        .katas-tabs .nav-link {
            border: 0;
            border-radius: 0;
            color: #8b93a3;
            padding: 0.85rem 1.25rem;
        }

        .katas-tabs .nav-link.active {
            color: #ffffff;
            background: #5aa7ee;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid pt-0 pb-3 eventos-browse">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                Revise los datos del formulario.
            </div>
        @endif

        <div class="row mb-2">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-2">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="fa-solid fa-clipboard-list"></i> Listado Katas
                                </h1>
                            </div>
                            <div class="col-md-4 text-end px-3 py-2">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create">
                                    <i class="bi bi-plus-lg"></i> <span>Crear</span>
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                                    <i class="bi bi-x-lg"></i> <span>Cerrar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center justify-content-end">
                            <div class="col-sm-3">
                                <input type="text" id="input-search" placeholder="Buscar..." class="form-control">
                            </div>
                        </div>

                        <div class="row" id="div-results" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-create" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('katas.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Crear kata</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @if(!old('editing_kata')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (!old('editing_kata'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="sistema_id" class="form-label">Sistema</label>
                                <input type="hidden" name="sistema_id" id="sistema_id_hidden" value="{{ old('sistema_id', 1) }}">
                                <select id="sistema_id" class="form-select @if(!old('editing_kata')) @error('sistema_id') is-invalid @enderror @endif" disabled>
                                    <option value="">Seleccione</option>
                                    @foreach ($sistemas as $sistema)
                                        <option value="{{ $sistema->id }}" {{ old('sistema_id', 1) == $sistema->id ? 'selected' : '' }}>
                                            {{ $sistema->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (!old('editing_kata'))
                                    @error('sistema_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-select @if(!old('editing_kata')) @error('estado') is-invalid @enderror @endif" required>
                                    <option value="Activo" {{ old('estado', 'Activo') === 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="Inactivo" {{ old('estado') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @if (!old('editing_kata'))
                                    @error('estado')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal-delete" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <form id="delete_form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold" id="modalDeleteLabel">Eliminar</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        Esta seguro de eliminar este kata?
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="submit" class="btn btn-danger">Si</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var timeout = null;

        $(document).ready(function () {
            list();

            $('#modal-create').on('show.bs.modal', function () {
                let activeTab = document.querySelector('#katas-system-tabs .nav-link.active');
                if (!activeTab) {
                    return;
                }

                let sistemaId = activeTab.dataset.sistemaId;
                $('#sistema_id').val(sistemaId);
                $('#sistema_id_hidden').val(sistemaId);
            });

            $('#input-search').on('keyup', function (event) {
                if (event.keyCode === 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#input-search').on('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    list();
                }, 600);
            });
        });

        function list() {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("katas.ajax.list") }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&_=${Date.now()}`,
                type: 'get',
                cache: false,
                success: function (result) {
                    $('#div-results').html(result);
                    let activeTab = document.querySelector('#katas-system-tabs .nav-link.active');
                    if (activeTab) {
                        $('#sistema_id').val(activeTab.dataset.sistemaId);
                        $('#sistema_id_hidden').val(activeTab.dataset.sistemaId);
                    }
                },
                error: function () {
                    $('#div-results').html('<div class="col-12"><div class="alert alert-danger mb-0">No se pudo cargar la lista.</div></div>');
                }
            });
        }

        function deleteItem(url) {
            $('#delete_form').attr('action', url);
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                @if (!old('editing_kata'))
                    let modal = new bootstrap.Modal(document.getElementById('modal-create'));
                    modal.show();
                @endif
            });
        @endif
    </script>
@endpush
