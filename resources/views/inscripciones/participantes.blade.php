@extends('layouts.app')

@section('title', 'Participantes')

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
                    <h1 class="h3 mb-0"><i class="bi bi-person-plus"></i> Participantes</h1>
                    <small class="text-muted">
                        {{ $torneo->nombre ?: 'Torneo sin nombre' }} / {{ $inscripcion->organizacion->nombre }}
                    </small>
                </div>
                <a href="{{ route('inscripciones.index', $torneo) }}" class="btn btn-warning text-white">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('inscripciones.participantes', [$torneo, $inscripcion]) }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="modalidad_id" class="form-label">Modalidad</label>
                        <select name="modalidad_id" id="modalidad_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($modalidades as $modalidad)
                                <option value="{{ $modalidad->id }}" {{ (string) request('modalidad_id') === (string) $modalidad->id ? 'selected' : '' }}>
                                    {{ $modalidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select name="categoria_id" id="categoria_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($categorias as $categoria)
                                <option
                                    value="{{ $categoria->id }}"
                                    data-modalidad-id="{{ $categoria->modalidad_id }}"
                                    {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}
                                >
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Inscribir participante</div>
                    <div class="card-body">
                        @if ($categoriasDisponibles->isEmpty())
                            <div class="alert alert-warning mb-0">No hay modalidades para el filtro seleccionado.</div>
                        @else
                            <form method="POST" action="{{ route('inscripciones.participantes.store', [$torneo, $inscripcion]) }}">
                                @csrf
                                <input type="hidden" name="modalidad_filtro_id" value="{{ request('modalidad_id') }}">
                                <input type="hidden" name="categoria_filtro_id" value="{{ request('categoria_id') }}">
                                <div class="mb-3">
                                    <label for="persona_ids" class="form-label">Competidores</label>
                                    <div id="persona_ids" class="border rounded px-2 py-2 @error('persona_ids') border-danger @enderror @error('persona_ids.*') border-danger @enderror" style="height: 96px; overflow-y: auto;">
                                        @forelse ($personas as $persona)
                                            @php
                                                $pesoCompetidor = $pesosCompetidores->get($persona->id);
                                            @endphp
                                            <div class="form-check py-1">
                                                <input type="checkbox" name="persona_ids[]" id="persona_id_{{ $persona->id }}" value="{{ $persona->id }}" class="form-check-input" {{ collect(old('persona_ids', []))->contains((string) $persona->id) ? 'checked' : '' }}>
                                                <label for="persona_id_{{ $persona->id }}" class="form-check-label">
                                                    {{ $persona->first_name }}{{ $persona->birth_date ? ' - ' . $persona->birth_date->diffInYears(now()) . ' años' : '' }}
                                                    @if ($mostrarPesoFiltro && $pesoCompetidor !== null)
                                                        / {{ number_format((float) $pesoCompetidor, 3) }} Kg
                                                    @endif
                                                </label>
                                            </div>
                                        @empty
                                            <div class="text-muted">No hay competidores disponibles para el filtro seleccionado.</div>
                                        @endforelse
                                    </div>
                                    @error('persona_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                    @error('persona_ids.*')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 48px; text-align: center;">Sel.</th>
                                                <th>Modalidad / categoria</th>
                                                <th style="width: 120px;">Costo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categoriasDisponibles as $index => $categoria)
                                                @php
                                                    $oldModalidades = collect(old('modalidades', []));
                                                    $oldMatch = $oldModalidades->firstWhere('categoria_id', (string) $categoria->id) ?: $oldModalidades->firstWhere('categoria_id', $categoria->id);
                                                    $shouldCheck = $oldMatch || $categoriasDisponibles->count() === 1;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" class="form-check-input js-modalidad-check" name="modalidades[{{ $index }}][categoria_id]" value="{{ $categoria->id }}" {{ $shouldCheck ? 'checked' : '' }}>
                                                        <input type="hidden" class="js-modalidad-id" name="modalidades[{{ $index }}][id]" value="{{ $categoria->modalidad_id }}" {{ $shouldCheck ? '' : 'disabled' }}>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $categoria->modalidad->nombre ?? 'Sin modalidad' }}</strong>{{ $categoria->genero ? ' - ' . $categoria->genero : '' }}<br>
                                                        <small class="text-muted">{{ $categoria->nombre }}</small>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm js-modalidad-costo" name="modalidades[{{ $index }}][costo]" value="{{ $oldMatch['costo'] ?? $torneo->costo_inscripcion_competidor }}" min="0" step="0.01" {{ $shouldCheck ? '' : 'disabled' }}>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @error('modalidades')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>Total: <span id="total-competidor">0.00</span></strong>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-lg"></i> Guardar
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold">Participantes inscritos</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Competidor</th>
                                        <th>Modalidades</th>
                                        <th style="width: 120px; text-align: center;">Total</th>
                                        <th style="width: 96px; text-align: center;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($competidores as $competidor)
                                        @php
                                            $competidorPeso = $pesosCompetidores->get($competidor->persona_id);
                                            $competidorTieneKumite = $competidor->modalidades->contains(function ($detalle) {
                                                return str_contains(mb_strtolower((string) optional($detalle->modalidad)->nombre), 'kumite');
                                            });
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ $competidor->persona->first_name }}{{ $competidor->persona->birth_date ? ' - ' . $competidor->persona->birth_date->diffInYears(now()) . ' años' : '' }}
                                                @if (($mostrarPesoFiltro || $competidorTieneKumite) && $competidorPeso !== null)
                                                    / {{ number_format((float) $competidorPeso, 3) }} Kg
                                                @endif
                                            </td>
                                            <td>
                                                @foreach ($competidor->modalidades as $detalle)
                                                    <div>
                                                        {{ $detalle->modalidad->nombre }}
                                                        @if ($detalle->categoria)
                                                            <small class="text-muted">/ {{ $detalle->categoria->nombre }}</small>
                                                        @endif
                                                        <span class="label label-info">{{ number_format((float) $detalle->costo, 2) }}</span>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                <span class="label label-success">{{ number_format((float) $competidor->total, 2) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-grid gap-1" style="grid-template-columns: repeat(2, 32px);">
                                                    <button type="button" class="btn btn-sm btn-info text-white d-inline-flex align-items-center justify-content-center p-1" style="width: 32px; height: 32px;" title="Editar monto" data-bs-toggle="modal" data-bs-target="#modal-edit-pago-{{ $competidor->id }}">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center justify-content-center p-1" style="width: 32px; height: 32px;" title="Eliminar competidor" data-bs-toggle="modal" data-bs-target="#modal-delete-participante-{{ $competidor->id }}">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No hay participantes inscritos para esta organizacion.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @foreach ($competidores as $competidor)
                            <div class="modal fade" id="modal-edit-pago-{{ $competidor->id }}" tabindex="-1" aria-labelledby="modalEditPagoLabel{{ $competidor->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" action="{{ route('inscripciones.participantes.pagos.update', [$torneo, $inscripcion, $competidor]) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title fw-bold" id="modalEditPagoLabel{{ $competidor->id }}">Editar monto del pago</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="fw-bold mb-3">{{ $competidor->persona->first_name }}</div>
                                                @foreach ($competidor->modalidades as $detalle)
                                                    <div class="mb-3">
                                                        <label for="costo_detalle_{{ $detalle->id }}" class="form-label">
                                                            {{ $detalle->modalidad->nombre }}
                                                            @if ($detalle->categoria)
                                                                <span class="text-muted">/ {{ $detalle->categoria->nombre }}</span>
                                                            @endif
                                                        </label>
                                                        <input type="number" name="costos[{{ $detalle->id }}]" id="costo_detalle_{{ $detalle->id }}" value="{{ old('costos.' . $detalle->id, $detalle->costo) }}" class="form-control" min="0" step="0.01" required>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-info text-white">
                                                    <i class="bi bi-check-lg"></i> Guardar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-delete-participante-{{ $competidor->id }}" tabindex="-1" aria-labelledby="modalDeleteParticipanteLabel{{ $competidor->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form method="POST" action="{{ route('inscripciones.participantes.destroy', [$torneo, $inscripcion, $competidor]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title fw-bold" id="modalDeleteParticipanteLabel{{ $competidor->id }}">Eliminar competidor</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="mb-1">Seguro que desea eliminar a este competidor de la inscripcion?</p>
                                                <strong>{{ $competidor->persona->first_name }}</strong>
                                                <small class="text-muted d-block mt-2">Tambien se eliminaran las modalidades registradas para este competidor en esta organizacion.</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#modalidad_id').on('change', filterCategoriasByModalidad);
            $('.js-modalidad-check').on('change', syncModalidadCosts);
            $('.js-modalidad-costo').on('input', updateCompetidorTotal);
            filterCategoriasByModalidad();
            syncModalidadCosts();
        });

        function filterCategoriasByModalidad() {
            const modalidadId = $('#modalidad_id').val();
            const categoriaSelect = $('#categoria_id');
            const selectedCategoria = categoriaSelect.find('option:selected');
            let selectedStillVisible = !selectedCategoria.val();

            categoriaSelect.find('option').each(function () {
                const option = $(this);
                const optionModalidadId = option.data('modalidad-id');
                const visible = !option.val() || !modalidadId || String(optionModalidadId) === String(modalidadId);

                option.prop('hidden', !visible).prop('disabled', !visible);

                if (visible && option.is(':selected')) {
                    selectedStillVisible = true;
                }
            });

            if (!selectedStillVisible) {
                categoriaSelect.val('');
            }
        }

        function updateCompetidorTotal() {
            let total = 0;
            $('.js-modalidad-check:checked').each(function () {
                let row = $(this).closest('tr');
                total += parseFloat(row.find('.js-modalidad-costo').val()) || 0;
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
    </script>
@endpush
