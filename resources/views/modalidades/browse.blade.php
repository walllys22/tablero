@extends('layouts.app')

@section('title', 'Modalidades')

@section('content')
    @php
        $closeRoute = request('return') === 'torneos' ? route('torneos.index') : route('dashboard');
    @endphp

    <div class="container-fluid pt-0 ps-0 pb-4 eventos-browse">
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
                    <div class="card-body p-1">
                        <div class="row g-0 align-items-center">
                            <div class="col-md-8 pe-3 pb-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="bi bi-list-check"></i> Modalidades
                                </h1>
                                <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                            </div>
                            <div class="col-md-4 text-end pe-3 pb-3">
                                <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#modal-create-modalidad">
                                    <i class="bi bi-plus-lg"></i> <span>Crear</span>
                                </button>
                                <a href="{{ $closeRoute }}" class="btn btn-warning text-white">
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
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-9">
                                <div class="dataTables_length" id="dataTable_length">
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

    <div class="modal fade" id="modal-create-categoria" tabindex="-1" aria-labelledby="modalCreateCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('categorias.store', ['torneo' => $torneo, 'return' => request('return')]) }}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateCategoriaLabel">Crear categoria</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="modalidad_categoria" class="form-label">Modalidad</label>
                                <select name="modalidad_id" id="modalidad_categoria" class="form-select @if(old('creating_categoria')) @error('modalidad_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($modalidades as $modalidad)
                                        <option value="{{ $modalidad->id }}" {{ old('creating_categoria') && old('modalidad_id') == $modalidad->id ? 'selected' : '' }}>
                                            {{ $modalidad->nombre }} - {{ $modalidad->genero }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('creating_categoria'))
                                    @error('modalidad_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="nombre_categoria" class="form-label">Categoria</label>
                                <input type="text" name="nombre" id="nombre_categoria" value="{{ old('creating_categoria') ? old('nombre') : '' }}" class="form-control @if(old('creating_categoria')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('creating_categoria'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label for="genero_categoria" class="form-label">Genero</label>
                                <select name="genero" id="genero_categoria" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="Masculino" {{ old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Mixto" {{ old('genero') === 'Mixto' ? 'selected' : '' }}>Mixto</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="orden_categoria" class="form-label">Orden</label>
                                <input type="number" name="orden" id="orden_categoria" value="{{ old('orden', 0) }}" class="form-control" min="0" max="9999">
                            </div>

                            <div class="col-md-3">
                                <label for="edad_desde_categoria" class="form-label">Edad desde</label>
                                <input type="number" name="edad_desde" id="edad_desde_categoria" value="{{ old('edad_desde') }}" class="form-control @if(old('creating_categoria')) @error('edad_desde') is-invalid @enderror @endif" min="0" max="99">
                            </div>

                            <div class="col-md-3">
                                <label for="edad_hasta_categoria" class="form-label">Edad hasta</label>
                                <input type="number" name="edad_hasta" id="edad_hasta_categoria" value="{{ old('edad_hasta') }}" class="form-control @if(old('creating_categoria')) @error('edad_hasta') is-invalid @enderror @endif" min="0" max="99">
                                @if (old('creating_categoria'))
                                    @error('edad_hasta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-3">
                                <label for="peso_desde_categoria" class="form-label">Peso desde</label>
                                <input type="number" name="peso_desde" id="peso_desde_categoria" value="{{ old('peso_desde') }}" class="form-control @if(old('creating_categoria')) @error('peso_desde') is-invalid @enderror @endif" min="0" step="0.01">
                            </div>

                            <div class="col-md-3">
                                <label for="peso_hasta_categoria" class="form-label">Peso hasta</label>
                                <input type="number" name="peso_hasta" id="peso_hasta_categoria" value="{{ old('peso_hasta') }}" class="form-control @if(old('creating_categoria')) @error('peso_hasta') is-invalid @enderror @endif" min="0" step="0.01">
                                @if (old('creating_categoria'))
                                    @error('peso_hasta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-12">
                                <label for="grado_categoria" class="form-label">Grado</label>
                                <input type="text" name="grado" id="grado_categoria" value="{{ old('grado') }}" class="form-control" maxlength="100">
                            </div>
                        </div>
                        <input type="hidden" name="creating_categoria" value="1">
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

    <div class="modal fade" id="modal-create-modalidad" tabindex="-1" aria-labelledby="modalCreateModalidadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('modalidades.store', ['torneo' => $torneo, 'return' => request('return')]) }}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateModalidadLabel">Crear modalidad</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="nombre_modalidad" class="form-label">Modalidad</label>
                                <input type="text" name="nombre" id="nombre_modalidad" value="{{ old('creating_modalidad') ? old('nombre') : '' }}" class="form-control @if(old('creating_modalidad')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('creating_modalidad'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label for="genero_modalidad" class="form-label">Genero</label>
                                <select name="genero" id="genero_modalidad" class="form-select @if(old('creating_modalidad')) @error('genero') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    <option value="Masculino" {{ old('creating_modalidad') && old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('creating_modalidad') && old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                                @if (old('creating_modalidad'))
                                    @error('genero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>
                        <input type="hidden" name="creating_modalidad" value="1">
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

            let url = '{{ route("modalidades.ajax.list", $torneo) }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}&return={{ urlencode(request('return', '')) }}`,
                type: 'get',
                success: function (result) {
                    $('#div-results').html(result);
                },
                error: function () {
                    $('#div-results').html('<div class="col-12"><div class="alert alert-danger mb-0">No se pudo cargar la lista.</div></div>');
                }
            });
        }

        @if ($errors->any() && old('creating_categoria'))
            document.addEventListener('DOMContentLoaded', function () {
                let modal = new bootstrap.Modal(document.getElementById('modal-create-categoria'));
                modal.show();
            });
        @elseif ($errors->any() && old('creating_modalidad'))
            document.addEventListener('DOMContentLoaded', function () {
                let modal = new bootstrap.Modal(document.getElementById('modal-create-modalidad'));
                modal.show();
            });
        @endif
    </script>
@endpush
