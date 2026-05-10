@extends('layouts.app')

@section('title', 'Ver modalidad')

@section('content')
    @php
        $formatCategoriaNombre = function ($nombre) {
            return str_replace(
                ["a\xC3\x83\xC6\x92\xC3\x82\xC2\xB1os", "a\xC3\x83\xC2\xB1os", 'anos', 'menor o igual', 'mayor o igual'],
                ["a\u{00F1}os", "a\u{00F1}os", "a\u{00F1}os", "\u{2264}", "\u{2265}"],
                $nombre
            );
        };
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
                                    <i class="bi bi-eye"></i> Ver modalidad
                                </h1>
                                <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                            </div>
                            <div class="col-md-4 text-end pe-3 pb-3">
                                <a href="{{ route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')]) }}" class="btn btn-warning text-white">
                                    <i class="bi bi-arrow-left"></i> <span>Volver</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <strong>Datos de la modalidad</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Torneo</label>
                                <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    {{ $torneo->nombre ?: 'Torneo sin nombre' }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Modalidad</label>
                                <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                    {{ $modalidad->nombre }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Modalidad</th>
                                        <th>Categorias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <strong>{{ $modalidad->nombre }}</strong>
                                        </td>
                                        <td>
                                            @forelse ($modalidad->categorias as $categoria)
                                                <div style="line-height: 1.6;">{{ $formatCategoriaNombre($categoria->nombre) }}</div>
                                            @empty
                                                <span class="text-muted">Sin categorias</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @foreach ($modalidad->categorias as $categoria)
            @php
                $isEditing = old('editing_categoria') == $categoria->id;
                $isKata = str_contains(mb_strtolower($modalidad->nombre), 'kata');
                $categoriaNombre = $formatCategoriaNombre($categoria->nombre);
                $pesoTipo = str_contains(mb_strtolower($categoria->nombre), 'mayor o igual') ? 'min' : 'max';
            @endphp

            <div class="modal fade js-modal-edit-categoria" id="modal-edit-categoria-{{ $categoria->id }}" tabindex="-1" aria-labelledby="modalEditCategoriaLabel{{ $categoria->id }}" aria-hidden="true" data-modalidad-nombre="{{ $modalidad->nombre }}">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('categorias.update', ['torneo' => $torneo, 'modalidad' => $modalidad, 'categoria' => $categoria, 'return' => request('return')]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="editing_categoria" value="{{ $categoria->id }}">

                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title fw-bold" id="modalEditCategoriaLabel{{ $categoria->id }}">Editar categoria</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-control-plaintext border-bottom fs-5">
                                            <span class="fw-semibold">Modalidad:</span>
                                            <span>{{ $modalidad->nombre }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nombre_categoria_{{ $categoria->id }}" class="form-label">Categoria</label>
                                        <input type="text" id="nombre_categoria_{{ $categoria->id }}" value="{{ $isEditing ? old('nombre') : $categoriaNombre }}" class="form-control js-edit-nombre-categoria @if($isEditing) @error('nombre') is-invalid @enderror @endif" readonly>
                                        <input type="hidden" name="nombre" class="js-edit-nombre-categoria-hidden" value="{{ $isEditing ? old('nombre') : $categoriaNombre }}">
                                        @if ($isEditing)
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <label for="genero_categoria_{{ $categoria->id }}" class="form-label">Genero</label>
                                        <select name="genero" id="genero_categoria_{{ $categoria->id }}" class="form-select js-edit-genero-categoria js-edit-categoria-field">
                                            <option value="">Seleccione</option>
                                            <option value="Masculino" {{ old('editing_categoria') == $categoria->id ? (old('genero') === 'Masculino' ? 'selected' : '') : ($categoria->genero === 'Masculino' ? 'selected' : '') }}>Masculino</option>
                                            <option value="Femenino" {{ old('editing_categoria') == $categoria->id ? (old('genero') === 'Femenino' ? 'selected' : '') : ($categoria->genero === 'Femenino' ? 'selected' : '') }}>Femenino</option>
                                            <option value="Mixto" {{ old('editing_categoria') == $categoria->id ? (old('genero') === 'Mixto' ? 'selected' : '') : ($categoria->genero === 'Mixto' ? 'selected' : '') }}>Mixto</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="edad_desde_categoria_{{ $categoria->id }}" class="form-label">Edad desde</label>
                                        <input type="number" name="edad_desde" id="edad_desde_categoria_{{ $categoria->id }}" value="{{ $isEditing ? old('edad_desde') : $categoria->edad_desde }}" class="form-control js-edit-edad-desde-categoria js-edit-categoria-field @if($isEditing) @error('edad_desde') is-invalid @enderror @endif" min="0" max="99">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="edad_hasta_categoria_{{ $categoria->id }}" class="form-label">Edad hasta</label>
                                        <input type="number" name="edad_hasta" id="edad_hasta_categoria_{{ $categoria->id }}" value="{{ $isEditing ? old('edad_hasta') : $categoria->edad_hasta }}" class="form-control js-edit-edad-hasta-categoria js-edit-categoria-field @if($isEditing) @error('edad_hasta') is-invalid @enderror @endif" min="0" max="99">
                                        @if ($isEditing)
                                            @error('edad_hasta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>

                                    <div class="col-md-3 js-edit-peso-categoria {{ $isKata ? 'd-none' : '' }}">
                                        <label for="peso_tipo_categoria_{{ $categoria->id }}" class="form-label">Tipo peso</label>
                                        <select name="peso_tipo" id="peso_tipo_categoria_{{ $categoria->id }}" class="form-select js-edit-peso-tipo js-edit-categoria-field" {{ $isKata ? 'disabled' : '' }}>
                                            <option value="max" {{ $isEditing ? (old('peso_tipo', 'max') === 'max' ? 'selected' : '') : ($pesoTipo === 'max' ? 'selected' : '') }}>Menor o igual</option>
                                            <option value="min" {{ $isEditing ? (old('peso_tipo') === 'min' ? 'selected' : '') : ($pesoTipo === 'min' ? 'selected' : '') }}>Mayor o igual</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 js-edit-peso-categoria {{ $isKata ? 'd-none' : '' }}">
                                        <label for="peso_hasta_categoria_{{ $categoria->id }}" class="form-label">Peso referencia</label>
                                        <input type="number" name="peso_hasta" id="peso_hasta_categoria_{{ $categoria->id }}" value="{{ $isEditing ? old('peso_hasta') : $categoria->peso_hasta }}" class="form-control js-edit-peso-hasta js-edit-categoria-field @if($isEditing) @error('peso_hasta') is-invalid @enderror @endif" min="0" step="0.01" {{ $isKata ? 'disabled' : '' }}>
                                        @if ($isEditing)
                                            @error('peso_hasta')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-info text-white">
                                    <i class="bi bi-check-lg"></i> Actualizar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.js-modal-edit-categoria').on('shown.bs.modal', function () {
                toggleEditPesoCategoria($(this));
            });

            $('.js-edit-categoria-field').on('input change', function () {
                updateEditCategoriaNombre($(this).closest('.js-modal-edit-categoria'));
            });

            @if ($errors->any() && old('editing_categoria'))
                let editCategoriaModal = document.getElementById('modal-edit-categoria-{{ old('editing_categoria') }}');
                if (editCategoriaModal) {
                    new bootstrap.Modal(editCategoriaModal).show();
                }
            @endif
        });

        function toggleEditPesoCategoria(modal) {
            let isKata = String(modal.data('modalidad-nombre') || '').toLowerCase().includes('kata');
            modal.find('.js-edit-peso-categoria').toggleClass('d-none', isKata);
            modal.find('.js-edit-peso-tipo, .js-edit-peso-hasta').prop('disabled', isKata);

            if (isKata) {
                modal.find('.js-edit-peso-hasta').val('');
            }

            updateEditCategoriaNombre(modal);
        }

        function updateEditCategoriaNombre(modal) {
            let modalidadNombre = modal.data('modalidad-nombre') || '';
            let isKata = String(modalidadNombre).toLowerCase().includes('kata');
            let peso = modal.find('.js-edit-peso-hasta').val();
            let genero = modal.find('.js-edit-genero-categoria').val();
            let edadDesde = modal.find('.js-edit-edad-desde-categoria').val();
            let edadHasta = modal.find('.js-edit-edad-hasta-categoria').val();
            let edadTexto = '';

            if (edadDesde && edadHasta) {
                edadTexto = `${edadDesde} a ${edadHasta} aÃ±os`;
            } else if (edadDesde) {
                edadTexto = `desde ${edadDesde} aÃ±os`;
            } else if (edadHasta) {
                edadTexto = `hasta ${edadHasta} aÃ±os`;
            }

            if (! genero && ! edadTexto && (! peso || isKata)) {
                modal.find('.js-edit-nombre-categoria, .js-edit-nombre-categoria-hidden').val('');
                return;
            }

            let parts = [];

            if (edadTexto) {
                parts.push(edadTexto);
            }

            if (genero) {
                parts.push(genero);
            }

            if (! isKata && peso) {
                let textoPeso = modal.find('.js-edit-peso-tipo').val() === 'min' ? '\u2265' : '\u2264';
                parts.push(`${textoPeso} a ${peso} kilos`);
            }

            let generatedName = parts.join(' ');
            generatedName = generatedName.replace(/a(?:\u00c3\u0192\u00c2\u00b1|\u00c3\u00b1|n)os/g, 'años');
            modal.find('.js-edit-nombre-categoria, .js-edit-nombre-categoria-hidden').val(generatedName);
        }
    </script>
@endpush
