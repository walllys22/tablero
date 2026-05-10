@extends('layouts.app')

@section('title', 'Inscripciones')

@section('content')
    <div class="container-fluid py-4 eventos-browse">
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
                            <div class="col-md-7 px-3 py-3">
                                <h1 class="h3 mb-0 text-dark">
                                    <i class="bi bi-person-check"></i> Inscripciones
                                </h1>
                                <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                            </div>
                            <div class="col-md-5 text-end px-3 py-3">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-organizacion">
                                    <i class="bi bi-building-add"></i> Organizacion
                                </button>
                                <a href="{{ route('torneos.index') }}" class="btn btn-warning text-white">
                                    <i class="bi bi-x-lg"></i> Cerrar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($organizacionesInscritas->isEmpty())
            <div class="alert alert-warning">
                Primero inscriba una organizacion al campeonato para habilitar el registro de competidores.
            </div>
        @elseif ($categorias->isEmpty())
            <div class="alert alert-warning">
                Este campeonato no tiene categorias registradas. Cree modalidades y categorias antes de inscribir competidores.
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

    <div class="modal fade" id="modal-create-organizacion" tabindex="-1" aria-labelledby="modalCreateOrganizacionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('inscripciones.organizaciones.store', $torneo) }}">
                @csrf
                <input type="hidden" name="creating_organizacion" value="1">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateOrganizacionLabel">Inscripcion Organizacion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="organizacion_id" class="form-label">Organizacion</label>
                            <select name="organizacion_id" id="organizacion_id" class="form-select @if(old('creating_organizacion')) @error('organizacion_id') is-invalid @enderror @endif" required>
                                <option value="">Seleccione</option>
                                @foreach ($organizaciones as $organizacion)
                                    <option value="{{ $organizacion->id }}" data-responsable="{{ $organizacion->persona ? $organizacion->persona->first_name : 'Sin responsable' }}" {{ old('organizacion_id') == $organizacion->id ? 'selected' : '' }}>
                                        {{ $organizacion->nombre }}{{ $organizacion->persona ? ' - ' . $organizacion->persona->first_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @if (old('creating_organizacion'))
                                @error('organizacion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                            <small class="text-muted d-block mt-1">Responsable: <span id="responsable-organizacion-preview">Seleccione una organizacion</span></small>
                        </div>
                        <div>
                            <label for="costo_organizacion" class="form-label">Costo</label>
                            <input type="number" name="costo" id="costo_organizacion" value="{{ old('creating_organizacion') ? old('costo') : $torneo->costo_inscripcion_organizacion }}" class="form-control @if(old('creating_organizacion')) @error('costo') is-invalid @enderror @endif" min="0" step="0.01" required>
                            @if (old('creating_organizacion'))
                                @error('costo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
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

    <div class="modal fade" id="modal-no-organizacion" tabindex="-1" aria-labelledby="modalNoOrganizacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="modalNoOrganizacionLabel">Organizacion requerida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    No se puede inscribir competidores si no tiene una organizacion inscrita en este campeonato.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-create-competidor" tabindex="-1" aria-labelledby="modalCreateCompetidorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('inscripciones.competidores.store', $torneo) }}">
                @csrf
                <input type="hidden" name="creating_competidor" value="1">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateCompetidorLabel">Inscripcion Competidor</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="inscripcion_organizacion_id" class="form-label">Organizacion inscrita</label>
                                <select name="inscripcion_organizacion_id" id="inscripcion_organizacion_id" class="form-select @if(old('creating_competidor')) @error('inscripcion_organizacion_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($organizacionesInscritas as $inscripcion)
                                        <option value="{{ $inscripcion->id }}" {{ old('inscripcion_organizacion_id') == $inscripcion->id ? 'selected' : '' }}>
                                            {{ $inscripcion->organizacion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('creating_competidor'))
                                    @error('inscripcion_organizacion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="persona_id" class="form-label">Competidor</label>
                                <select name="persona_id" id="persona_id" class="form-select @if(old('creating_competidor')) @error('persona_id') is-invalid @enderror @endif" required>
                                    <option value="">Seleccione</option>
                                    @foreach ($personas as $persona)
                                        <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                            {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if (old('creating_competidor'))
                                    @error('persona_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 60px; text-align: center;">Sel.</th>
                                        <th>Modalidad</th>
                                        <th style="width: 180px;">Costo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categorias as $index => $categoria)
                                        @php
                                            $oldModalidades = collect(old('modalidades', []));
                                            $oldMatch = $oldModalidades->firstWhere('categoria_id', (string) $categoria->id) ?: $oldModalidades->firstWhere('categoria_id', $categoria->id);
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input js-modalidad-check" name="modalidades[{{ $index }}][categoria_id]" value="{{ $categoria->id }}" {{ $oldMatch ? 'checked' : '' }}>
                                                <input type="hidden" class="js-modalidad-id" name="modalidades[{{ $index }}][id]" value="{{ $categoria->modalidad_id }}" {{ $oldMatch ? '' : 'disabled' }}>
                                            </td>
                                            <td>
                                                <strong>{{ $categoria->modalidad->nombre ?? 'Sin modalidad' }}</strong>{{ $categoria->genero ? ' - ' . $categoria->genero : '' }}<br>
                                                <small class="text-muted">{{ $categoria->nombre }}</small>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control form-control-sm js-modalidad-costo" name="modalidades[{{ $index }}][costo]" value="{{ $oldMatch['costo'] ?? $torneo->costo_inscripcion_competidor }}" min="0" step="0.01" {{ $oldMatch ? '' : 'disabled' }}>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if (old('creating_competidor'))
                            @error('modalidades')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        @endif
                        <div class="text-end fw-bold">
                            Total competidor: <span id="total-competidor">0.00</span>
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
@endsection

@push('scripts')
    <script>
        var countPage = 10;
        var timeout = null;

        $(document).ready(function () {
            list();
            updateCompetidorTotal();

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

            $('.js-modalidad-check').on('change', syncModalidadCosts);
            $('.js-modalidad-costo').on('input', updateCompetidorTotal);
            $('#organizacion_id').on('change', updateResponsableOrganizacion);
            syncModalidadCosts();
            updateResponsableOrganizacion();
        });

        function list(page = 1) {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("inscripciones.ajax.list", $torneo) }}';
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

        function updateCompetidorTotal() {
            let total = 0;
            $('.js-modalidad-check:checked').each(function () {
                let row = $(this).closest('tr');
                let costo = parseFloat(row.find('.js-modalidad-costo').val()) || 0;
                total += costo;
            });

            $('#total-competidor').text(total.toFixed(2));
        }

        function syncModalidadCosts() {
            $('.js-modalidad-check').each(function () {
                let row = $(this).closest('tr');
                row.find('.js-modalidad-costo').prop('disabled', !this.checked);
                row.find('.js-modalidad-id').prop('disabled', !this.checked);
            });

            updateCompetidorTotal();
        }

        function openCompetidorModal(inscripcionOrganizacionId) {
            if (!inscripcionOrganizacionId) {
                new bootstrap.Modal(document.getElementById('modal-no-organizacion')).show();
                return;
            }

            $('#inscripcion_organizacion_id').val(inscripcionOrganizacionId);
            new bootstrap.Modal(document.getElementById('modal-create-competidor')).show();
        }

        function updateResponsableOrganizacion() {
            let selected = $('#organizacion_id option:selected');
            let responsable = selected.data('responsable') || 'Seleccione una organizacion';
            $('#responsable-organizacion-preview').text(responsable);
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function () {
                @if (old('creating_organizacion'))
                    new bootstrap.Modal(document.getElementById('modal-create-organizacion')).show();
                @elseif (old('creating_competidor'))
                    new bootstrap.Modal(document.getElementById('modal-create-competidor')).show();
                @endif
            });
        @endif
    </script>
@endpush
