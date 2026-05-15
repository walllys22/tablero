@extends('layouts.app')

@section('title', 'Competidores')

@section('content')
    <div class="container-fluid py-4">
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

        <div class="row mb-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 px-3 py-3">
                                <h1 class="h3 mb-1 text-dark">
                                    <i class="bi bi-person-check"></i> Competidores
                                </h1>
                                <div class="text-muted">
                                    {{ $organizacion->nombre }}
                                </div>
                            </div>
                            <div class="col-md-4 text-end px-3 py-3">
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-create" {{ $personas->isEmpty() ? 'disabled' : '' }}>
                                    <i class="bi bi-plus-lg"></i> <span>Agregar</span>
                                </button>
                                <a href="{{ route('organizaciones.index') }}" class="btn btn-warning text-white">
                                    <i class="bi bi-x-lg"></i> <span>Cerrar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($personas->isEmpty())
            <div class="alert alert-info">
                No hay personas activas disponibles para agregar a esta organizacion.
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-9">
                                <label class="d-flex align-items-center gap-2 mb-0">
                                    Mostrar
                                    <select id="select-paginate" class="form-select form-select-sm w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    registros
                                </label>
                            </div>
                            <div class="col-sm-3 mt-2 mt-sm-0">
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
            <form method="POST" action="{{ route('organizaciones.competidores.store', $organizacion) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Agregar competidor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-9">
                                <label for="persona_ids" class="form-label">Personas</label>
                                <div id="persona_ids" class="border rounded px-2 py-2 @error('persona_ids') border-danger @enderror @error('persona_ids.*') border-danger @enderror" style="height: 178px; overflow-y: auto;">
                                    @foreach ($personas as $persona)
                                        <div class="form-check py-1">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-8">
                                                    <input type="checkbox" name="persona_ids[]" id="persona_id_{{ $persona->id }}" value="{{ $persona->id }}" class="form-check-input" {{ in_array($persona->id, old('persona_ids', [])) ? 'checked' : '' }}>
                                                    <label for="persona_id_{{ $persona->id }}" class="form-check-label">
                                                        {{ $persona->first_name }} - {{ $persona->birth_date->age }} años
                                                    </label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" name="pesos[{{ $persona->id }}]" value="{{ old('pesos.' . $persona->id) }}" class="form-control form-control-sm @error('pesos.' . $persona->id) is-invalid @enderror" placeholder="Peso Kg" min="0" max="999.999" step="0.001">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('persona_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('persona_ids.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input type="checkbox" name="status" id="status" value="1" class="form-check-input" {{ old('status', 1) ? 'checked' : '' }}>
                                    <label for="status" class="form-check-label">Activo</label>
                                </div>
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
        <div class="modal-dialog">
            <form id="delete_form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalDeleteLabel">Eliminar competidor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        Seguro que desea eliminar este competidor de la organizacion?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var countPage = 10;
        var timeout = null;

        $(document).ready(function () {
            list();

            $('#input-search').on('keyup', function (event) {
                if (event.keyCode === 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#select-paginate').change(function () {
                countPage = $(this).val();
                list();
            });

            $('#input-search').on('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    list();
                }, 600);
            });
        });

        function list(page = 1) {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("organizaciones.competidores.ajax.list", $organizacion) }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}&_=${Date.now()}`,
                type: 'get',
                cache: false,
                success: function (result) {
                    $('#div-results').html(result);
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
                let modal = new bootstrap.Modal(document.getElementById('modal-create'));
                modal.show();
            });
        @endif
    </script>
@endpush
