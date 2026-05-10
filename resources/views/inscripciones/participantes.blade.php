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
                                <option value="{{ $categoria->id }}" {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}>
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
                                <div class="mb-3">
                                    <label for="persona_id" class="form-label">Competidor</label>
                                    <select name="persona_id" id="persona_id" class="form-select @error('persona_id') is-invalid @enderror" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($personas as $persona)
                                            <option value="{{ $persona->id }}" {{ old('persona_id') == $persona->id ? 'selected' : '' }}>
                                                {{ $persona->first_name }}{{ $persona->ci ? ' - CI ' . $persona->ci : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('persona_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
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
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($competidores as $competidor)
                                        <tr>
                                            <td>{{ $competidor->persona->first_name }}{{ $competidor->persona->ci ? ' - CI ' . $competidor->persona->ci : '' }}</td>
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No hay participantes inscritos para esta organizacion.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.js-modalidad-check').on('change', syncModalidadCosts);
            $('.js-modalidad-costo').on('input', updateCompetidorTotal);
            syncModalidadCosts();
        });

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
