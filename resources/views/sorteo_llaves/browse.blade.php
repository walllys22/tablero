@extends('layouts.app')

@section('title', 'Sorteo Llaves')

@section('content')
    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">
                {{ session('status') }}
            </div>
        @endif

        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="fa-solid fa-code-branch"></i> Sorteo Llaves
                    </h1>
                    <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                    <i class="bi bi-x-lg"></i> Cerrar
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('sorteo-llaves.index', $torneo) }}" id="form-sortear-llave" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="modalidad_id" class="form-label">Modalidad</label>
                        <select name="modalidad_id" id="modalidad_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach ($modalidades as $modalidad)
                                <option value="{{ $modalidad->id }}" {{ (string) request('modalidad_id') === (string) $modalidad->id ? 'selected' : '' }}>
                                    {{ $modalidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select name="categoria_id" id="categoria_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach ($modalidades as $modalidad)
                                @foreach ($modalidad->categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        data-modalidad-id="{{ $modalidad->id }}"
                                        {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <input type="hidden" name="sortear" value="1">
                        <input type="hidden" name="seed" value="{{ $seed }}">
                        <button type="submit" id="btn-sortear" class="btn btn-success" {{ $sorteoActual ? 'disabled' : '' }}>
                            <i class="bi bi-shuffle"></i> Sortear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="sorteos-list-wrapper">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-bold">Categorias sorteadas</div>
                <div class="card-body">
                    <div class="row g-2 align-items-end mb-3">
                        <div class="col-md-8">
                            <label class="d-flex align-items-center gap-2 mb-0">
                                Mostrar
                                <select class="form-select form-select-sm w-auto js-sorteos-page-size">
                                    <option value="5" selected>5</option>
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                registros
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label for="buscar-categoria-sorteada" class="form-label mb-1">Buscar categoria</label>
                            <input type="text" id="buscar-categoria-sorteada" class="form-control js-sorteos-search" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 70px; text-align: center;">Nro.</th>
                                    <th>Modalidad</th>
                                    <th>Categoria</th>
                                    <th style="width: 120px; text-align: center;">Area</th>
                                    <th style="width: 150px; text-align: center;">Fecha</th>
                                    <th style="width: 180px; text-align: center;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="js-sorteos-tbody">
                                @php $numeroPorModalidad = []; @endphp
                                @forelse ($sorteos as $sorteo)
                                    @php
                                        $modalidadKey = $sorteo->modalidad_id ?: 'sin-modalidad';
                                        $numeroPorModalidad[$modalidadKey] = ($numeroPorModalidad[$modalidadKey] ?? 0) + 1;
                                    @endphp
                                    <tr class="js-sorteo-row" data-search="{{ mb_strtolower(($sorteo->modalidad->nombre ?? '') . ' ' . ($sorteo->categoria->nombre ?? '')) }}">
                                        <td class="text-center fw-bold">{{ $numeroPorModalidad[$modalidadKey] }}</td>
                                        <td>{{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }}</td>
                                        <td>{{ $sorteo->categoria->nombre ?? 'Sin categoria' }}</td>
                                        <td class="text-center">
                                            @if ($sorteo->area)
                                                <span class="area-badge area-color-{{ (($sorteo->area - 1) % 8) + 1 }}">
                                                    Area {{ $sorteo->area }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin area</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $sorteo->updated_at ? $sorteo->updated_at->format('d/m/Y H:i') : '' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('sorteo-llaves.graphic', [$torneo, 'modalidad_id' => $sorteo->modalidad_id, 'categoria_id' => $sorteo->categoria_id, 'seed' => $sorteo->seed]) }}" class="btn btn-sm btn-primary p-1" title="Ver llaves">
                                                <i class="bi bi-diagram-3"></i>
                                            </a>
                                            @if (($sorteo->resultados_kumite_count ?? 0) > 0)
                                                <a href="{{ route('sorteo-llaves.resultados', [$torneo, $sorteo]) }}" class="btn btn-sm btn-info text-white p-1" title="Llaves realizadas">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary p-1" title="Sin llaves realizadas" disabled>
                                                    <i class="bi bi-clipboard-check"></i>
                                                </button>
                                            @endif
                                            @if (($torneo->cantidad_areas ?? 1) >= 2)
                                                <button type="button" class="btn btn-sm btn-success p-1" title="Designar area"
                                                    data-bs-toggle="modal" data-bs-target="#modal-area-{{ $sorteo->id }}">
                                                    <i class="bi bi-grid-3x3-gap"></i>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger p-1" title="Eliminar sorteo"
                                                data-bs-toggle="modal" data-bs-target="#modal-delete-sorteo-{{ $sorteo->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="js-sorteos-empty">
                                        <td colspan="6" class="text-center text-muted">No hay categorias sorteadas.</td>
                                    </tr>
                                @endforelse
                                <tr class="js-sorteos-no-results d-none">
                                    <td colspan="6" class="text-center text-muted">No hay categorias sorteadas que coincidan con la busqueda.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                        <div class="text-muted js-sorteos-summary"></div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0 js-sorteos-pagination"></ul>
                        </nav>
                    </div>
                </div>
            </div>

            @if (($torneo->cantidad_areas ?? 1) >= 2)
                @foreach ($sorteos as $sorteo)
                    <div class="modal fade" id="modal-area-{{ $sorteo->id }}" tabindex="-1"
                        aria-labelledby="modalAreaLabel{{ $sorteo->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('sorteo-llaves.area.update', [$torneo, $sorteo]) }}">
                                @csrf
                                @method('PATCH')

                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title fw-bold" id="modalAreaLabel{{ $sorteo->id }}">
                                            Designar area
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Cerrar"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Categoria</label>
                                            <div class="form-control-plaintext border rounded px-3 py-2 bg-light">
                                                {{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }} /
                                                {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                                            </div>
                                        </div>

                                        <div>
                                            <label for="area_{{ $sorteo->id }}" class="form-label">Area</label>
                                            <select name="area" id="area_{{ $sorteo->id }}" class="form-select" required>
                                                <option value="">Seleccione</option>
                                                @for ($area = 1; $area <= (int) $torneo->cantidad_areas; $area++)
                                                    <option value="{{ $area }}" {{ (int) $sorteo->area === $area ? 'selected' : '' }}>
                                                        Area {{ $area }}
                                                    </option>
                                                @endfor
                                            </select>
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
                @endforeach
            @endif

            @foreach ($sorteos as $sorteo)
                <div class="modal fade" id="modal-delete-sorteo-{{ $sorteo->id }}" tabindex="-1"
                    aria-labelledby="modalDeleteSorteoLabel{{ $sorteo->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" action="{{ route('sorteo-llaves.destroy', [$torneo, $sorteo]) }}">
                            @csrf
                            @method('DELETE')

                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold" id="modalDeleteSorteoLabel{{ $sorteo->id }}">
                                        Eliminar sorteo
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    Seguro que desea eliminar el sorteo de:
                                    <div class="fw-bold mt-2">
                                        {{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }} /
                                        {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        La categoria volvera a estar disponible para sortear nuevamente si tiene 2 o mas competidores inscritos.
                                    </small>
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input js-confirm-delete-sorteo"
                                            id="confirm-delete-sorteo-{{ $sorteo->id }}"
                                            data-target="#btn-delete-sorteo-{{ $sorteo->id }}">
                                        <label for="confirm-delete-sorteo-{{ $sorteo->id }}" class="form-check-label">
                                            Estoy seguro de eliminar
                                        </label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" id="btn-delete-sorteo-{{ $sorteo->id }}" class="btn btn-danger" disabled>
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($categoria)
            <div class="alert alert-info">
                {{ $categoria->modalidad->nombre }} / {{ $categoria->nombre }}:
                {{ $competidores->count() }} competidor(es) inscritos.
                @if ($sorteoActual)
                    <strong>Esta categoria ya fue sorteada.</strong>
                @endif
            </div>
        @endif

        @if (request('sortear') && $competidores->count() < 2)
            <div class="alert alert-warning">
                Se necesitan al menos 2 competidores inscritos en la categoria para generar llaves.
            </div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .area-badge {
            border-radius: 6px;
            color: #fff;
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            min-width: 58px;
            padding: 6px 8px;
            text-align: center;
        }

        .area-color-1 {
            background: #198754;
        }

        .area-color-2 {
            background: #0d6efd;
        }

        .area-color-3 {
            background: #dc3545;
        }

        .area-color-4 {
            background: #fd7e14;
        }

        .area-color-5 {
            background: #6f42c1;
        }

        .area-color-6 {
            background: #0dcaf0;
            color: #073642;
        }

        .area-color-7 {
            background: #d63384;
        }

        .area-color-8 {
            background: #495057;
        }

        .bracket-wrapper {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding-bottom: 12px;
        }

        .bracket-round {
            min-width: 260px;
        }

        .bracket-round-title {
            background: #0d6efd;
            border-radius: 6px 6px 0 0;
            color: #fff;
            font-weight: 700;
            padding: 8px 10px;
            text-align: center;
        }

        .bracket-match {
            border: 1px solid #d9e2ef;
            border-radius: 6px;
            margin-top: 14px;
            overflow: hidden;
            background: #fff;
        }

        .bracket-player {
            min-height: 58px;
            padding: 8px 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .bracket-player:last-child {
            border-bottom: 0;
        }

        .bracket-player small {
            display: block;
            color: #6c757d;
            margin-top: 2px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form-sortear-llave');
            const modalidad = document.getElementById('modalidad_id');
            const categoria = document.getElementById('categoria_id');
            const sortearButton = document.getElementById('btn-sortear');
            const defaultButtonHtml = sortearButton.innerHTML;

            function filterCategorias() {
                const modalidadId = modalidad.value;
                let selectedVisible = !categoria.value;

                categoria.querySelectorAll('option').forEach(function (option) {
                    const optionModalidadId = option.dataset.modalidadId;
                    const visible = !option.value || !modalidadId || String(optionModalidadId) === String(modalidadId);

                    option.hidden = !visible;
                    option.disabled = !visible;

                    if (visible && option.selected) {
                        selectedVisible = true;
                    }
                });

                if (!selectedVisible) {
                    categoria.value = '';
                }

                sortearButton.disabled = !modalidad.value || !categoria.value;
            }

            function setSortingState(isSorting) {
                sortearButton.disabled = true;
                sortearButton.innerHTML = isSorting
                    ? '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Sorteando...'
                    : defaultButtonHtml;
            }

            function fillAvailableSelects(data, previousModalidadId) {
                modalidad.innerHTML = '<option value="">Seleccione</option>';
                categoria.innerHTML = '<option value="">Seleccione</option>';

                data.modalidades.forEach(function (item) {
                    const modalidadOption = document.createElement('option');
                    modalidadOption.value = item.id;
                    modalidadOption.textContent = item.nombre;
                    modalidad.appendChild(modalidadOption);

                    item.categorias.forEach(function (categoriaItem) {
                        const categoriaOption = document.createElement('option');
                        categoriaOption.value = categoriaItem.id;
                        categoriaOption.textContent = categoriaItem.nombre;
                        categoriaOption.dataset.modalidadId = categoriaItem.modalidad_id;
                        categoria.appendChild(categoriaOption);
                    });
                });

                const modalidadStillAvailable = Array.from(modalidad.options)
                    .some(function (option) {
                        return option.value && String(option.value) === String(previousModalidadId);
                    });

                modalidad.value = modalidadStillAvailable ? previousModalidadId : '';
                categoria.value = '';
                filterCategorias();
                sortearButton.innerHTML = defaultButtonHtml;
            }

            function refreshCategorias(previousModalidadId) {
                return fetch('{{ route('sorteo-llaves.categorias', $torneo) }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('No se pudo actualizar la lista.');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        fillAvailableSelects(data, previousModalidadId);
                    });
            }

            function refreshSorteosList() {
                return fetch('{{ route('sorteo-llaves.index', $torneo) }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('No se pudo actualizar la lista de sorteos.');
                        }

                        return response.text();
                    })
                    .then(function (html) {
                        const documentHtml = new DOMParser().parseFromString(html, 'text/html');
                        const nextWrapper = documentHtml.getElementById('sorteos-list-wrapper');
                        const currentWrapper = document.getElementById('sorteos-list-wrapper');

                        if (nextWrapper && currentWrapper) {
                            currentWrapper.innerHTML = nextWrapper.innerHTML;
                            initSorteosListControls();
                        }
                    });
            }

            function initSorteosListControls() {
                const wrapper = document.getElementById('sorteos-list-wrapper');

                if (!wrapper) {
                    return;
                }

                const searchInput = wrapper.querySelector('.js-sorteos-search');
                const pageSizeSelect = wrapper.querySelector('.js-sorteos-page-size');
                const pagination = wrapper.querySelector('.js-sorteos-pagination');
                const summary = wrapper.querySelector('.js-sorteos-summary');
                const noResults = wrapper.querySelector('.js-sorteos-no-results');
                const rows = Array.from(wrapper.querySelectorAll('.js-sorteo-row'));
                let currentPage = 1;

                function render() {
                    const search = (searchInput?.value || '').trim().toLowerCase();
                    const pageSize = parseInt(pageSizeSelect?.value || '10', 10);
                    const filteredRows = rows.filter(function (row) {
                        return !search || (row.dataset.search || '').includes(search);
                    });
                    const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    rows.forEach(function (row) {
                        row.classList.add('d-none');
                    });

                    filteredRows
                        .slice((currentPage - 1) * pageSize, currentPage * pageSize)
                        .forEach(function (row) {
                            row.classList.remove('d-none');
                        });

                    if (noResults) {
                        noResults.classList.toggle('d-none', filteredRows.length !== 0 || rows.length === 0);
                    }

                    if (summary) {
                        if (filteredRows.length === 0) {
                            summary.textContent = rows.length === 0 ? '' : 'No hay registros para mostrar.';
                        } else {
                            const from = ((currentPage - 1) * pageSize) + 1;
                            const to = Math.min(currentPage * pageSize, filteredRows.length);
                            summary.textContent = `Mostrando del ${from} al ${to} de ${filteredRows.length} registros.`;
                        }
                    }

                    if (pagination) {
                        pagination.innerHTML = '';

                        for (let page = 1; page <= totalPages; page++) {
                            const item = document.createElement('li');
                            item.className = `page-item ${page === currentPage ? 'active' : ''}`;
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'page-link';
                            button.textContent = page;
                            button.addEventListener('click', function () {
                                currentPage = page;
                                render();
                            });
                            item.appendChild(button);
                            pagination.appendChild(item);
                        }
                    }
                }

                searchInput?.addEventListener('input', function () {
                    currentPage = 1;
                    render();
                });
                pageSizeSelect?.addEventListener('change', function () {
                    currentPage = 1;
                    render();
                });
                render();
            }

            document.addEventListener('change', function (event) {
                if (!event.target.classList.contains('js-confirm-delete-sorteo')) {
                    return;
                }

                const button = document.querySelector(event.target.dataset.target);

                if (button) {
                    button.disabled = !event.target.checked;
                }
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!modalidad.value || !categoria.value) {
                    filterCategorias();
                    return;
                }

                const previousModalidadId = modalidad.value;
                const url = `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;

                setSortingState(true);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('No se pudo sortear la categoria.');
                        }

                        return response.json();
                    })
                    .then(function () {
                        return Promise.all([
                            refreshCategorias(previousModalidadId),
                            refreshSorteosList()
                        ]);
                    })
                    .catch(function () {
                        sortearButton.innerHTML = defaultButtonHtml;
                        filterCategorias();
                        alert('No se pudo sortear la categoria.');
                    });
            });

            modalidad.addEventListener('change', filterCategorias);
            categoria.addEventListener('change', filterCategorias);
            filterCategorias();
            initSorteosListControls();
        });
    </script>
@endpush
