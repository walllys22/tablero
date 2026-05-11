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

        @if (session('warning'))
            <div class="modal fade" id="modal-warning-modalidad" tabindex="-1" aria-labelledby="modalWarningModalidadLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title fw-bold" id="modalWarningModalidadLabel">No se puede eliminar</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            {{ session('warning') }}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Aceptar</button>
                        </div>
                    </div>
                </div>
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
                                <a href="{{ route('modalidades.print', ['torneo' => $torneo, 'return' => request('return')]) }}" class="btn btn-primary me-2">
                                    <i class="bi bi-printer"></i> <span>Imprimir</span>
                                </a>
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

    @php
        $oldModalidadNombre = old('modalidad_id')
            ? optional($modalidades->firstWhere('id', (int) old('modalidad_id')))->nombre
            : '';
    @endphp

    <div class="modal fade" id="modal-create-categoria" tabindex="-1" aria-labelledby="modalCreateCategoriaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('categorias.store', ['torneo' => $torneo, 'return' => request('return')]) }}">
                @csrf

                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateCategoriaLabel">Crear categoria</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="form-control-plaintext border-bottom fs-5">
                                    <span class="fw-semibold">Modalidad:</span>
                                    <span id="modalidad_nombre_referencia">{{ $oldModalidadNombre }}</span>
                                </div>
                                <input type="hidden" name="modalidad_id" id="modalidad_id_categoria" value="{{ old('modalidad_id') }}">
                                @if (old('creating_categoria'))
                                    @error('modalidad_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-12 js-prefijo-categoria d-none">
                                <label for="prefijo_categoria" class="form-label">Prefijo</label>
                                <input type="text" name="prefijo" id="prefijo_categoria" value="{{ old('prefijo') }}" class="form-control" placeholder="">
                            </div>

                            <div class="col-md-6">
                                <label for="genero_categoria" class="form-label">Genero</label>
                                <select name="genero" id="genero_categoria" class="form-select">
                                    <option value="">Seleccione</option>
                                    <option value="Masculino" {{ old('genero') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('genero') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Mixto" {{ old('genero') === 'Mixto' ? 'selected' : '' }}>Mixto</option>
                                </select>
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

                            <div class="col-md-12">
                                <label for="nombre_categoria" class="form-label">Categoria</label>
                                <input type="text" id="nombre_categoria" value="{{ old('creating_categoria') ? old('nombre') : '' }}" class="form-control @if(old('creating_categoria')) @error('nombre') is-invalid @enderror @endif" readonly>
                                <input type="hidden" name="nombre" id="nombre_categoria_hidden" value="{{ old('creating_categoria') ? old('nombre') : '' }}">
                                @if (old('creating_categoria'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-3 js-peso-categoria">
                                <label for="peso_tipo_categoria" class="form-label">Tipo peso</label>
                                <select name="peso_tipo" id="peso_tipo_categoria" class="form-select">
                                    <option value="max" {{ old('peso_tipo', 'max') === 'max' ? 'selected' : '' }}>Menor o igual</option>
                                    <option value="min" {{ old('peso_tipo') === 'min' ? 'selected' : '' }}>Mayor o igual</option>
                                </select>
                            </div>

                            <div class="col-md-3 js-peso-categoria">
                                <label for="peso_hasta_categoria" class="form-label">Peso referencia</label>
                                <input type="number" name="peso_hasta" id="peso_hasta_categoria" value="{{ old('peso_hasta') }}" class="form-control @if(old('creating_categoria')) @error('peso_hasta') is-invalid @enderror @endif" min="0" step="0.001">
                                @if (old('creating_categoria'))
                                    @error('peso_hasta')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
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
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateModalidadLabel">Crear modalidad</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="nombre_modalidad" class="form-label">Modalidad</label>
                                <input type="text" name="nombre" id="nombre_modalidad" value="{{ old('creating_modalidad') ? old('nombre') : '' }}" class="form-control @if(old('creating_modalidad')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required>
                                @if (old('creating_modalidad'))
                                    @error('nombre')
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
            togglePesoCategoria(@json($oldModalidadNombre));

            $('#modal-create-categoria').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);

                if (! button.length) {
                    return;
                }

                let modalidadNombre = button.data('modalidad-nombre') || '';
                $('#modalidad_id_categoria').val(button.data('modalidad-id') || '');
                $('#modalidad_nombre_referencia').text(modalidadNombre);
                $('#nombre_categoria, #nombre_categoria_hidden').val('');
                $('#genero_categoria').val('');
                $('#prefijo_categoria').val('');
                $('#edad_desde_categoria, #edad_hasta_categoria, #peso_hasta_categoria').val('');
                $('#peso_tipo_categoria').val('max');
                togglePesoCategoria(modalidadNombre);
            });

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

            $('#prefijo_categoria, #genero_categoria, #edad_desde_categoria, #edad_hasta_categoria, #peso_tipo_categoria, #peso_hasta_categoria').on('input change', updateCategoriaNombre);
        });

        function togglePesoCategoria(modalidadNombre) {
            let isKata = String(modalidadNombre || '').toLowerCase().includes('kata');
            $('.js-peso-categoria').toggleClass('d-none', isKata);
            $('#peso_tipo_categoria, #peso_hasta_categoria').prop('disabled', isKata);

            // Toggle Prefijo
            $('.js-prefijo-categoria').toggleClass('d-none', !isKata);
            $('#prefijo_categoria').prop('disabled', !isKata);

            if (isKata) {
                $('#peso_hasta_categoria').val('');
            }

            updateCategoriaNombre();
        }

        function updateCategoriaNombre() {
            let modalidadNombre = $('#modalidad_nombre_referencia').text();
            let nombreInput = $('#nombre_categoria');
            let isKata = String(modalidadNombre || '').toLowerCase().includes('kata');
            let prefijo = $('#prefijo_categoria').val();
            let peso = $('#peso_hasta_categoria').val();
            let genero = $('#genero_categoria').val();
            let edadDesde = $('#edad_desde_categoria').val();
            let edadHasta = $('#edad_hasta_categoria').val();
            let edadTexto = '';

            if (edadDesde && edadHasta) {
                edadTexto = `${edadDesde} a ${edadHasta} años`;
            } else if (edadDesde) {
                edadTexto = `desde ${edadDesde} años`;
            } else if (edadHasta) {
                edadTexto = `hasta ${edadHasta} años`;
            }

            if (! genero && ! edadTexto && (! peso || isKata) && (! prefijo || ! isKata)) {
                nombreInput.val('');
                $('#nombre_categoria_hidden').val('');
                return;
            }

            let parts = [];

            if (isKata && prefijo) {
                parts.push(prefijo);
            }

            if (edadTexto) {
                parts.push(edadTexto);
            }

            if (genero) {
                parts.push(genero);
            }

            if (! isKata && peso) {
                let textoPeso = $('#peso_tipo_categoria').val() === 'min' ? '\u2265' : '\u2264';
                parts.push(`${textoPeso} a ${formatPesoVisual(peso)}`);
            }

            let generatedName = parts.join(' ');
            nombreInput.val(generatedName);
            $('#nombre_categoria_hidden').val(generatedName);
        }

        function formatPesoVisual(peso) {
            let value = Number(peso);
            let kilos = Math.floor(value);
            let gramos = Math.round((value - kilos) * 1000);

            if (gramos === 1000) {
                kilos++;
                gramos = 0;
            }

            if (gramos === 0) {
                return `${kilos} Kilos`;
            }

            return `${kilos} Kilos y ${gramos} gramos`;
        }

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

        @if (session('warning'))
            document.addEventListener('DOMContentLoaded', function () {
                let warningModal = document.getElementById('modal-warning-modalidad');
                if (warningModal) {
                    new bootstrap.Modal(warningModal).show();
                }
            });
        @endif
    </script>
@endpush
