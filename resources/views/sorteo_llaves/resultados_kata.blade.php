@extends('layouts.app')

@section('title', 'Resultados de Kata')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="bi bi-clipboard-check"></i> Resultados de Kata
                    </h1>
                    <small class="text-muted">
                        {{ $torneo->nombre ?: 'Torneo sin nombre' }} /
                        {{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }} /
                        {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                    </small>
                </div>
                <a href="{{ route('sorteo-llaves.index', $torneo) }}" class="btn btn-warning text-white">
                    <i class="bi bi-x-lg"></i> Cerrar
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold">Resultados de combate</div>
            <div class="card-body">
                @if ($sorteo->resultadosKata->isEmpty())
                    <div class="alert alert-info mb-0">Todavia no hay resultados de Kata.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 90px; text-align: center;">Llave</th>
                                    <th>Rojo</th>
                                    <th>Azul</th>
                                    <th style="width: 120px; text-align: center;">Resultado</th>
                                    <th>Detalle rojo</th>
                                    <th>Detalle azul</th>
                                    <th style="width: 190px; text-align: center;">Ganador</th>
                                    <th style="width: 130px; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sorteo->resultadosKata as $resultado)
                                    @php
                                        $ganadorClass = ((int) $resultado->puntaje_azul > (int) $resultado->puntaje_rojo)
                                            ? 'bg-primary'
                                            : 'bg-danger';
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold">{{ ((int) $resultado->indice_combate) + 1 }}</td>
                                        <td>
                                            <strong>{{ $resultado->competidor_rojo ?: 'Sin competidor' }}</strong>
                                            @if ($resultado->kiken_rojo)
                                                <span class="badge bg-dark ms-1">Kiken</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $resultado->competidor_azul ?: 'Sin competidor' }}</strong>
                                            @if ($resultado->kiken_azul)
                                                <span class="badge bg-dark ms-1">Kiken</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $resultado->puntaje_rojo }}</span>
                                            <span class="mx-1">-</span>
                                            <span class="badge bg-primary">{{ $resultado->puntaje_azul }}</span>
                                        </td>
                                        <td>
                                            <div><strong>Kata Nro.:</strong> {{ $resultado->kata_numero_rojo ?: '-' }}</div>
                                            <div><strong>Nombre:</strong> {{ $resultado->kata_nombre_rojo ?: '-' }}</div>
                                            <div><strong>Kiken:</strong> {{ $resultado->kiken_rojo ? 'Si' : 'No' }}</div>
                                        </td>
                                        <td>
                                            <div><strong>Kata Nro.:</strong> {{ $resultado->kata_numero_azul ?: '-' }}</div>
                                            <div><strong>Nombre:</strong> {{ $resultado->kata_nombre_azul ?: '-' }}</div>
                                            <div><strong>Kiken:</strong> {{ $resultado->kiken_azul ? 'Si' : 'No' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if ($resultado->ganador)
                                                <span class="badge {{ $ganadorClass }}">{{ $resultado->ganador }}</span>
                                            @else
                                                <span class="text-muted">Sin ganador</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-primary js-edit-kata-result"
                                                    data-sorteo-id="{{ $sorteo->id }}"
                                                    data-indice-combate="{{ $resultado->indice_combate }}"
                                                    data-llave="{{ ((int) $resultado->indice_combate) + 1 }}"
                                                    data-competidor-rojo="{{ $resultado->competidor_rojo }}"
                                                    data-competidor-azul="{{ $resultado->competidor_azul }}"
                                                    data-kata-numero-rojo="{{ $resultado->kata_numero_rojo }}"
                                                    data-kata-numero-azul="{{ $resultado->kata_numero_azul }}"
                                                    data-kata-nombre-rojo="{{ $resultado->kata_nombre_rojo }}"
                                                    data-kata-nombre-azul="{{ $resultado->kata_nombre_azul }}"
                                                    data-puntaje-rojo="{{ $resultado->puntaje_rojo }}"
                                                    data-puntaje-azul="{{ $resultado->puntaje_azul }}"
                                                    data-kiken-rojo="{{ $resultado->kiken_rojo ? '1' : '0' }}"
                                                    data-kiken-azul="{{ $resultado->kiken_azul ? '1' : '0' }}"
                                                    data-ganador="{{ $resultado->ganador }}"
                                                    data-ganador-color="{{ $resultado->ganador_color }}"
                                                    data-realizado-at="{{ $resultado->realizado_at ? $resultado->realizado_at->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i') }}">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger js-delete-kata-result"
                                                    data-sorteo-id="{{ $sorteo->id }}"
                                                    data-indice-combate="{{ $resultado->indice_combate }}"
                                                    data-llave="{{ ((int) $resultado->indice_combate) + 1 }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-edit-kata-result" tabindex="-1" aria-labelledby="modalEditKataResultLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <form id="form-edit-kata-result" class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="modalEditKataResultLabel">Editar resultado Kata</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sorteo_id">
                    <input type="hidden" name="indice_combate">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-4 border-danger rounded p-3 h-100">
                                <h6 class="fw-bold">Rojo</h6>
                                <div class="mb-2">
                                    <label class="form-label">Competidor</label>
                                    <input type="text" name="competidor_rojo" class="form-control">
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Puntaje</label>
                                        <input type="number" name="puntaje_rojo" class="form-control" min="0" step="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kata Nro.</label>
                                        <input type="text" name="kata_numero_rojo" class="form-control">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="kiken_rojo" id="edit_kiken_rojo" class="form-check-input">
                                            <label for="edit_kiken_rojo" class="form-check-label">Kiken</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label">Nombre Kata</label>
                                    <input type="text" name="kata_nombre_rojo" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-start border-4 border-primary rounded p-3 h-100">
                                <h6 class="fw-bold">Azul</h6>
                                <div class="mb-2">
                                    <label class="form-label">Competidor</label>
                                    <input type="text" name="competidor_azul" class="form-control">
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Puntaje</label>
                                        <input type="number" name="puntaje_azul" class="form-control" min="0" step="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kata Nro.</label>
                                        <input type="text" name="kata_numero_azul" class="form-control">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" name="kiken_azul" id="edit_kiken_azul" class="form-check-input">
                                            <label for="edit_kiken_azul" class="form-check-label">Kiken</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <label class="form-label">Nombre Kata</label>
                                    <input type="text" name="kata_nombre_azul" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Ganador</label>
                            <input type="text" name="ganador" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Color ganador</label>
                            <select name="ganador_color" class="form-select">
                                <option value="">Seleccione</option>
                                <option value="rojo">Rojo</option>
                                <option value="azul">Azul</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fecha</label>
                            <input type="datetime-local" name="realizado_at" class="form-control">
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3 d-none" id="edit-kata-error"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const guardarCombateKataUrl = @json(route('tablero.kata.combates.store'));
            const eliminarCombateKataUrl = @json(route('tablero.kata.combates.destroy'));
            const editKataModalElement = document.getElementById('modal-edit-kata-result');
            const editKataForm = document.getElementById('form-edit-kata-result');
            const editKataError = document.getElementById('edit-kata-error');
            const editKataModal = editKataModalElement && window.bootstrap ? new bootstrap.Modal(editKataModalElement) : null;

            function setFormValue(formElement, name, value) {
                const field = formElement.elements[name];

                if (!field) {
                    return;
                }

                if (field.type === 'checkbox') {
                    field.checked = value === '1' || value === 1 || value === true;
                    return;
                }

                field.value = value ?? '';
            }

            function actualizarGanadorKataEditado(forzarPorPuntaje = true) {
                if (!editKataForm) {
                    return;
                }

                const rojo = parseInt(editKataForm.elements.puntaje_rojo.value || '0', 10);
                const azul = parseInt(editKataForm.elements.puntaje_azul.value || '0', 10);
                let color = editKataForm.elements.ganador_color.value;

                if (forzarPorPuntaje && rojo !== azul) {
                    color = rojo > azul ? 'rojo' : 'azul';
                    editKataForm.elements.ganador_color.value = color;
                }

                if (color === 'rojo') {
                    editKataForm.elements.ganador.value = editKataForm.elements.competidor_rojo.value;
                } else if (color === 'azul') {
                    editKataForm.elements.ganador.value = editKataForm.elements.competidor_azul.value;
                }
            }

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-edit-kata-result');

                if (!button || !editKataForm) {
                    return;
                }

                editKataForm.reset();
                editKataError?.classList.add('d-none');
                if (editKataError) {
                    editKataError.textContent = '';
                }

                Object.entries({
                    sorteo_id: button.dataset.sorteoId,
                    indice_combate: button.dataset.indiceCombate,
                    competidor_rojo: button.dataset.competidorRojo,
                    competidor_azul: button.dataset.competidorAzul,
                    kata_numero_rojo: button.dataset.kataNumeroRojo,
                    kata_numero_azul: button.dataset.kataNumeroAzul,
                    kata_nombre_rojo: button.dataset.kataNombreRojo,
                    kata_nombre_azul: button.dataset.kataNombreAzul,
                    puntaje_rojo: button.dataset.puntajeRojo,
                    puntaje_azul: button.dataset.puntajeAzul,
                    kiken_rojo: button.dataset.kikenRojo,
                    kiken_azul: button.dataset.kikenAzul,
                    ganador: button.dataset.ganador,
                    ganador_color: button.dataset.ganadorColor,
                    realizado_at: button.dataset.realizadoAt,
                }).forEach(function ([name, value]) {
                    setFormValue(editKataForm, name, value);
                });

                actualizarGanadorKataEditado(false);
                editKataModal?.show();
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-delete-kata-result');

                if (!button) {
                    return;
                }

                if (!confirm(`Eliminar resultado de la llave ${button.dataset.llave}? La llave quedara pendiente.`)) {
                    return;
                }

                const originalHtml = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';

                fetch(eliminarCombateKataUrl, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        sorteo_id: button.dataset.sorteoId,
                        indice_combate: parseInt(button.dataset.indiceCombate || '0', 10),
                    }),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok) {
                                throw new Error(data.message || 'No se pudo eliminar el resultado.');
                            }

                            return data;
                        });
                    })
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function (error) {
                        button.disabled = false;
                        button.innerHTML = originalHtml;
                        alert(error.message || 'No se pudo eliminar el resultado.');
                    });
            });

            editKataForm?.elements.puntaje_rojo?.addEventListener('input', function () {
                actualizarGanadorKataEditado(true);
            });
            editKataForm?.elements.puntaje_azul?.addEventListener('input', function () {
                actualizarGanadorKataEditado(true);
            });
            editKataForm?.elements.ganador_color?.addEventListener('change', function () {
                actualizarGanadorKataEditado(false);
            });
            editKataForm?.elements.competidor_rojo?.addEventListener('input', function () {
                actualizarGanadorKataEditado(false);
            });
            editKataForm?.elements.competidor_azul?.addEventListener('input', function () {
                actualizarGanadorKataEditado(false);
            });

            editKataForm?.addEventListener('submit', function (event) {
                event.preventDefault();
                actualizarGanadorKataEditado(true);

                const submitButton = editKataForm.querySelector('button[type="submit"]');
                const originalHtml = submitButton.innerHTML;
                const payload = {
                    sorteo_id: editKataForm.elements.sorteo_id.value || null,
                    indice_combate: parseInt(editKataForm.elements.indice_combate.value || '0', 10),
                    competidor_rojo: editKataForm.elements.competidor_rojo.value,
                    competidor_azul: editKataForm.elements.competidor_azul.value,
                    kata_numero_rojo: editKataForm.elements.kata_numero_rojo.value,
                    kata_numero_azul: editKataForm.elements.kata_numero_azul.value,
                    kata_nombre_rojo: editKataForm.elements.kata_nombre_rojo.value,
                    kata_nombre_azul: editKataForm.elements.kata_nombre_azul.value,
                    puntaje_rojo: parseInt(editKataForm.elements.puntaje_rojo.value || '0', 10),
                    puntaje_azul: parseInt(editKataForm.elements.puntaje_azul.value || '0', 10),
                    kiken_rojo: editKataForm.elements.kiken_rojo.checked,
                    kiken_azul: editKataForm.elements.kiken_azul.checked,
                    ganador: editKataForm.elements.ganador.value,
                    ganador_color: editKataForm.elements.ganador_color.value || null,
                    realizado_at: editKataForm.elements.realizado_at.value || null,
                };

                editKataError?.classList.add('d-none');
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Guardando...';

                fetch(guardarCombateKataUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                    body: JSON.stringify(payload),
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok) {
                                throw new Error(data.message || 'No se pudo guardar la llave Kata.');
                            }

                            return data;
                        });
                    })
                    .then(function () {
                        window.location.reload();
                    })
                    .catch(function (error) {
                        if (editKataError) {
                            editKataError.textContent = error.message || 'No se pudo guardar la llave Kata.';
                            editKataError.classList.remove('d-none');
                        } else {
                            alert(error.message || 'No se pudo guardar la llave Kata.');
                        }
                    })
                    .finally(function () {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalHtml;
                    });
            });
        });
    </script>
@endpush
