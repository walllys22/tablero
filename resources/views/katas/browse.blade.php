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

        .kata-toast {
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .24);
            display: none;
            font-weight: 700;
            left: 50%;
            min-width: 320px;
            padding: 12px 18px;
            position: fixed;
            text-align: center;
            top: 18px;
            transform: translateX(-50%);
            z-index: 2000;
        }

        .kata-toast.visible {
            display: block;
        }

        .kata-toast-warning {
            background: #f5e8c7;
            color: #000000;
        }

        .kata-toast-danger {
            background: #dc3545;
            color: #ffffff;
        }

        .js-kata-orden-row {
            cursor: grab;
        }

        .js-kata-orden-row.dragging {
            opacity: .55;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid pt-0 pb-3 eventos-browse">
        <div id="kataToast" class="kata-toast" role="alert" aria-live="assertive"></div>

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
                                <button type="button" class="btn btn-primary" id="btn-open-orden-katas" data-bs-toggle="modal" data-bs-target="#modal-orden-katas">
                                    <i class="bi bi-sort-down"></i> <span>Ordenar</span>
                                </button>
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
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-9">
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
                            <div class="col-md-3 mt-2 mt-md-0">
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
                <input type="hidden" name="paginate" id="create_paginate" value="{{ session('katas_paginate', 10) }}">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="modalCreateLabel">Crear kata</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="numero" class="form-label">Nro. Kata</label>
                                <input type="number" name="numero" id="numero" value="{{ old('numero') }}" class="form-control @if(!old('editing_kata')) @error('numero') is-invalid @enderror @endif" min="1" required>
                                @if (!old('editing_kata'))
                                    @error('numero')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" class="form-control @if(!old('editing_kata')) @error('nombre') is-invalid @enderror @endif" maxlength="255" required data-kata-name-field data-numero-field="#numero" data-sistema-field="#sistema_id_hidden">
                                @if (!old('editing_kata'))
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                            <div class="col-md-3">
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
                                <input type="hidden" name="estado" value="Activo">
                                <select id="estado" class="form-select @if(!old('editing_kata')) @error('estado') is-invalid @enderror @endif" disabled>
                                    <option value="Activo" selected>Activo</option>
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

    <div class="modal fade" id="modal-orden-katas" tabindex="-1" aria-labelledby="modalOrdenKatasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('katas.orden.update') }}" id="form-orden-katas">
                @csrf
                @method('PATCH')
                <input type="hidden" name="sistema_id" id="orden_sistema_id">

                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold" id="modalOrdenKatasLabel">
                            <i class="bi bi-sort-down"></i> Ordenar katas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        @error('orden')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                        <div class="alert alert-info">
                            Arrastre los katas para definir el orden dentro del sistema seleccionado.
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 44px;"></th>
                                        <th style="width: 70px; text-align: center;">#</th>
                                        <th>Kata</th>
                                        <th style="width: 130px; text-align: center;">Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="katas-orden-list"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar orden
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var countPage = {{ (int) session('katas_paginate', 10) }};
        var timeout = null;
        var kataToastTimeout = null;
        var activeSistemaId = @json(session('katas_active_system_id'));
        var initialKatasPage = {{ (int) session('katas_page', 1) }};

        $(document).ready(function () {
            $('#select-paginate').val(String(countPage));
            $('#create_paginate').val(countPage);
            list(initialKatasPage, activeSistemaId);

            $('#modal-create').on('show.bs.modal', function () {
                $('#nombre').val('');
                $('#estado').val('Activo');
                $('#create_paginate').val(countPage);

                let activeTab = document.querySelector('#katas-system-tabs .nav-link.active');
                if (!activeTab) {
                    $('#numero').val('');
                    return;
                }

                let sistemaId = activeTab.dataset.sistemaId;
                $('#sistema_id').val(sistemaId);
                $('#sistema_id_hidden').val(sistemaId);
                $('#numero').val(sugerirProximoNumeroKata(sistemaId));
            });

            $('#input-search').on('keyup', function (event) {
                if (event.keyCode === 13) {
                    clearTimeout(timeout);
                    list();
                }
            });

            $('#select-paginate').change(function () {
                countPage = $(this).val();
                $('#create_paginate').val(countPage);
                list();
            });

            $('#input-search').on('input', function () {
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    list();
                }, 600);
            });

            $(document).on('focus', '[data-kata-name-field]', function () {
                validarNumeroKataAntesDeNombre(this);
            });

            $('#modal-orden-katas').on('show.bs.modal', prepararModalOrdenKatas);

            $(document).on('shown.bs.tab', '#katas-system-tabs .nav-link', function (event) {
                activeSistemaId = event.target.dataset.sistemaId;
                list(1, activeSistemaId);
            });

            let draggedKataOrdenRow = null;

            document.addEventListener('dragstart', function (event) {
                const row = event.target.closest('#katas-orden-list .js-kata-orden-row');

                if (!row) {
                    return;
                }

                draggedKataOrdenRow = row;
                row.classList.add('dragging');
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', row.dataset.id);
            });

            document.addEventListener('dragover', function (event) {
                const row = event.target.closest('#katas-orden-list .js-kata-orden-row');

                if (!row || !draggedKataOrdenRow || row === draggedKataOrdenRow) {
                    return;
                }

                event.preventDefault();
                const rect = row.getBoundingClientRect();
                const insertAfter = event.clientY > rect.top + rect.height / 2;
                row.parentNode.insertBefore(draggedKataOrdenRow, insertAfter ? row.nextSibling : row);
                actualizarNumerosOrdenKatas();
            });

            document.addEventListener('dragend', function () {
                if (draggedKataOrdenRow) {
                    draggedKataOrdenRow.classList.remove('dragging');
                    draggedKataOrdenRow = null;
                }

                actualizarNumerosOrdenKatas();
            });
        });

        function mostrarKataToast(mensaje, tipo) {
            let toast = document.getElementById('kataToast');

            if (!toast) {
                return;
            }

            clearTimeout(kataToastTimeout);
            toast.textContent = mensaje;
            toast.className = `kata-toast visible kata-toast-${tipo}`;
            kataToastTimeout = setTimeout(function () {
                toast.classList.remove('visible');
            }, 2800);
        }

        function existeNumeroKataDuplicado(numero, sistemaId, kataIdActual) {
            let duplicado = false;

            document.querySelectorAll('[data-kata-order-row]').forEach(function (row) {
                if (duplicado) {
                    return;
                }

                if (String(row.dataset.sistemaId) !== String(sistemaId)) {
                    return;
                }

                if (kataIdActual && String(row.dataset.kataId) === String(kataIdActual)) {
                    return;
                }

                duplicado = String(row.dataset.numero) === String(numero);
            });

            return duplicado;
        }

        function sugerirProximoNumeroKata(sistemaId) {
            let mayor = 0;

            document.querySelectorAll('[data-kata-order-row]').forEach(function (row) {
                if (String(row.dataset.sistemaId) !== String(sistemaId)) {
                    return;
                }

                let numero = parseInt(row.dataset.numero, 10);

                if (!Number.isNaN(numero) && numero > mayor) {
                    mayor = numero;
                }
            });

            return mayor + 1;
        }

        function prepararModalOrdenKatas() {
            const activeTab = document.querySelector('#katas-system-tabs .nav-link.active');
            const tbody = document.getElementById('katas-orden-list');

            tbody.innerHTML = '';

            if (!activeTab) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay sistema seleccionado.</td></tr>';
                return;
            }

            const sistemaId = activeTab.dataset.sistemaId;
            document.getElementById('orden_sistema_id').value = sistemaId;

            const rows = Array.from(document.querySelectorAll(`[data-kata-order-row][data-sistema-id="${sistemaId}"]`));

            if (rows.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No hay katas para ordenar.</td></tr>';
                return;
            }

            rows
                .sort(function (a, b) {
                    return (parseInt(a.dataset.numero, 10) || 0) - (parseInt(b.dataset.numero, 10) || 0);
                })
                .forEach(function (row, index) {
                    const tr = document.createElement('tr');
                    tr.className = 'js-kata-orden-row';
                    tr.dataset.id = row.dataset.kataId;
                    tr.draggable = true;
                    tr.innerHTML = `
                        <td class="text-center text-muted">
                            <i class="bi bi-grip-vertical"></i>
                            <input type="hidden" name="orden[]" value="${row.dataset.kataId}">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-primary js-kata-orden-numero">${index + 1}</span>
                        </td>
                        <td>
                            <div class="fw-semibold">${row.dataset.nombre || ''}</div>
                            <small class="text-muted">Nuevo Nro. Kata: <span class="js-kata-orden-nro">${index + 1}</span></small>
                        </td>
                        <td class="text-center">
                            <span class="badge ${row.dataset.estado === 'Activo' ? 'bg-success' : 'bg-danger'}">${row.dataset.estado || ''}</span>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
        }

        function actualizarNumerosOrdenKatas() {
            document.querySelectorAll('#katas-orden-list .js-kata-orden-row').forEach(function (row, index) {
                const badge = row.querySelector('.js-kata-orden-numero');

                if (badge) {
                    badge.textContent = index + 1;
                }

                const numero = row.querySelector('.js-kata-orden-nro');

                if (numero) {
                    numero.textContent = index + 1;
                }
            });
        }

        function validarNumeroKataAntesDeNombre(nombreInput) {
            let numeroInput = document.querySelector(nombreInput.dataset.numeroField);
            let sistemaInput = document.querySelector(nombreInput.dataset.sistemaField);
            let kataIdActual = nombreInput.dataset.kataId || '';
            let numero = numeroInput ? numeroInput.value.trim() : '';
            let sistemaId = sistemaInput ? sistemaInput.value : '';

            if (numero === '' || Number(numero) === 0) {
                mostrarKataToast('Revise el Numero de Kata', 'warning');
                return;
            }

            if (existeNumeroKataDuplicado(numero, sistemaId, kataIdActual)) {
                mostrarKataToast('Revise el Numero de Kata esta duplicado', 'danger');
            }
        }

        function list(page = 1, sistemaId = null) {
            $('#div-results').html('<div class="col-12 text-center text-muted py-5">Cargando...</div>');

            let url = '{{ route("katas.ajax.list") }}';
            let search = $('#input-search').val() ? $('#input-search').val() : '';
            let requestedSistemaId = sistemaId || activeSistemaId || '';

            $.ajax({
                url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&sistema_id=${encodeURIComponent(requestedSistemaId)}&page=${page}&_=${Date.now()}`,
                type: 'get',
                cache: false,
                success: function (result) {
                    $('#div-results').html(result);
                    let activeTab = document.querySelector('#katas-system-tabs .nav-link.active');
                    if (activeTab) {
                        activeSistemaId = activeTab.dataset.sistemaId;
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
