@extends('layouts.app')

@section('title', 'Sorteo Llaves')

@section('content')
    <div class="container-fluid py-4">
        @if (session('status'))
            <div class="alert alert-success js-auto-dismiss">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
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
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select name="categoria_id" id="categoria_id" class="form-select" required>
                            <option value="">Seleccione</option>
                            @foreach ($modalidades as $modalidad)
                                @foreach ($modalidad->categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        data-modalidad-id="{{ $modalidad->id }}"
                                        data-sorteada="{{ $categoriaIdsSorteadas->contains((int) $categoria->id) ? '1' : '0' }}"
                                        {{ (string) request('categoria_id') === (string) $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sistema_sorteo" class="form-label">Sistema</label>
                        <select name="sistema_sorteo" id="sistema_sorteo" class="form-select" required>
                            <option value="eliminacion_directa" {{ request('sistema_sorteo', 'eliminacion_directa') === 'eliminacion_directa' ? 'selected' : '' }}>Eliminacion directa</option>
                            <option value="round_robin" {{ request('sistema_sorteo') === 'round_robin' ? 'selected' : '' }}>Round Robin</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <input type="hidden" name="sortear" value="1" id="input-sortear">
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
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <span class="fw-bold">Categorias sorteadas</span>
                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#modal-orden-sorteos" {{ $sorteos->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-sort-down"></i> Ordenar
                    </button>
                </div>
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
                                        <td>
                                            <div>{{ $sorteo->categoria->nombre ?? 'Sin categoria' }}</div>
                                            @if ($sorteo->categoria_estado === 'realizada')
                                                <span class="badge bg-success mt-1">Realizada</span>
                                            @elseif ($sorteo->categoria_estado === 'ejecucion')
                                                <span class="badge bg-warning text-dark mt-1">En ejecucion</span>
                                            @else
                                                <span class="badge bg-secondary mt-1">Pendiente</span>
                                            @endif
                                        </td>
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
                                            <button type="button" class="btn btn-sm btn-success p-1" title="Mostrar llaves sorteadas"
                                                data-bs-toggle="modal" data-bs-target="#modal-llaves-sorteadas-{{ $sorteo->id }}">
                                                <i class="bi bi-list-check"></i>
                                            </button>
                                            @if (($sorteo->resultados_kumite_count ?? 0) > 0)
                                                <a href="{{ route('sorteo-llaves.resultados', [$torneo, $sorteo]) }}" class="btn btn-sm btn-info text-white p-1" title="Llaves realizadas">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </a>
                                                <a href="{{ route('tablero.kumite.podio', ['sorteo_id' => $sorteo->id]) }}" class="btn btn-sm btn-warning text-white p-1" title="Ver podio">
                                                    <i class="bi bi-trophy"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary p-1" title="Sin llaves realizadas" disabled>
                                                    <i class="bi bi-clipboard-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-secondary p-1" title="Podio no disponible" disabled>
                                                    <i class="bi bi-trophy"></i>
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
                <div class="modal fade" id="modal-llaves-sorteadas-{{ $sorteo->id }}" tabindex="-1"
                    aria-labelledby="modalLlavesSorteadasLabel{{ $sorteo->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title fw-bold" id="modalLlavesSorteadasLabel{{ $sorteo->id }}">
                                    Llaves sorteadas
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <div class="fw-semibold">{{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }}</div>
                                    <small class="text-muted">{{ $sorteo->categoria->nombre ?? 'Sin categoria' }}</small>
                                </div>
                                @if ($sorteo->categoria_completa)
                                    @php $podioModal = $sorteo->podio_modal ?? []; @endphp
                                    <div class="podio-horizontal mb-3">
                                        @if (! empty($podioModal['oro']))
                                            <div class="podio-horizontal-item podio-oro">
                                                <span>1</span>
                                                <strong>Oro</strong>
                                                <div>{{ $podioModal['oro'] }}</div>
                                            </div>
                                        @endif
                                        @if (! empty($podioModal['plata']))
                                            <div class="podio-horizontal-item podio-plata">
                                                <span>2</span>
                                                <strong>Plata</strong>
                                                <div>{{ $podioModal['plata'] }}</div>
                                            </div>
                                        @endif
                                        @if (! empty($podioModal['bronce_1']))
                                            <div class="podio-horizontal-item podio-bronce">
                                                <span>3</span>
                                                <strong>Bronce</strong>
                                                <div>{{ $podioModal['bronce_1'] }}</div>
                                            </div>
                                        @endif
                                        @if (! empty($podioModal['bronce_2']))
                                            <div class="podio-horizontal-item podio-bronce">
                                                <span>3</span>
                                                <strong>Bronce</strong>
                                                <div>{{ $podioModal['bronce_2'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="bracket-wrapper">
                                    @php
                                        $resultadosPorIndice = $sorteo->resultadosKumite->keyBy('indice_combate');
                                        $indiceCombateModal = 0;
                                    @endphp
                                    @foreach (($sorteo->llaves ?? []) as $ronda)
                                        <div class="bracket-round">
                                            <div class="bracket-round-title">{{ $ronda['nombre'] ?? 'Ronda' }}</div>
                                            @foreach (($ronda['combates'] ?? []) as $combate)
                                                @php
                                                    $resultadoCombate = $resultadosPorIndice->get($indiceCombateModal);
                                                    $llaveRealizada = (bool) $resultadoCombate;
                                                @endphp
                                                @if ($sorteo->categoria_completa && ! $llaveRealizada)
                                                    @php $indiceCombateModal++; @endphp
                                                    @continue
                                                @endif
                                                <div class="bracket-match">
                                                    <div class="bracket-status">
                                                        <span class="badge {{ $llaveRealizada ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $llaveRealizada ? 'Realizada' : 'Pendiente' }}
                                                        </span>
                                                        @if ($resultadoCombate)
                                                            <div class="bracket-result">
                                                                <strong>Resultado:</strong>
                                                                {{ $resultadoCombate->competidor_rojo ?: ($combate['a']['nombre'] ?? 'Rojo') }}
                                                                {{ $resultadoCombate->puntaje_rojo }}
                                                                -
                                                                {{ $resultadoCombate->puntaje_azul }}
                                                                {{ $resultadoCombate->competidor_azul ?: ($combate['b']['nombre'] ?? 'Azul') }}
                                                            </div>
                                                            @if ($resultadoCombate->ganador)
                                                                <div class="bracket-result">
                                                                    <strong>Ganador:</strong> {{ $resultadoCombate->ganador }}
                                                                </div>
                                                            @endif
                                                            <div class="bracket-result">
                                                                <strong>Senshu:</strong>
                                                                @if ($resultadoCombate->senshu === 'rojo')
                                                                    {{ $resultadoCombate->competidor_rojo ?: ($combate['a']['nombre'] ?? 'Rojo') }}
                                                                @elseif ($resultadoCombate->senshu === 'azul')
                                                                    {{ $resultadoCombate->competidor_azul ?: ($combate['b']['nombre'] ?? 'Azul') }}
                                                                @else
                                                                    No
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="bracket-player">
                                                        <strong>{{ $combate['a']['nombre'] ?? 'BYE' }}</strong>
                                                        @if (! empty($combate['a']['organizacion']))
                                                            <small>{{ $combate['a']['organizacion'] }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="bracket-player">
                                                        <strong>{{ $combate['b']['nombre'] ?? (($combate['bye'] ?? false) ? 'BYE' : 'Por definir') }}</strong>
                                                        @if (! empty($combate['b']['organizacion']))
                                                            <small>{{ $combate['b']['organizacion'] }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                @php $indiceCombateModal++; @endphp
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

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

            <div class="modal fade" id="modal-orden-sorteos" tabindex="-1" aria-labelledby="modalOrdenSorteosLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('sorteo-llaves.orden.update', $torneo) }}" id="form-orden-sorteos">
                        @csrf
                        @method('PATCH')

                        <div class="modal-content">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title fw-bold" id="modalOrdenSorteosLabel">
                                    <i class="bi bi-sort-down"></i> Ordenar categorias sorteadas
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                @error('orden')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                <div class="alert alert-info">
                                    Arrastre las categorias para definir el orden de competencia. Las categorias con combates realizados quedan bloqueadas.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width: 44px;"></th>
                                                <th style="width: 70px; text-align: center;">#</th>
                                                <th>Modalidad / categoria</th>
                                                <th style="width: 130px; text-align: center;">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sorteos-orden-list">
                                            @foreach ($sorteos as $index => $sorteo)
                                                @php
                                                    $bloqueado = (int) ($sorteo->resultados_kumite_count ?? 0) > 0;
                                                @endphp
                                                <tr class="js-orden-row {{ $bloqueado ? 'table-light' : '' }}"
                                                    data-id="{{ $sorteo->id }}"
                                                    data-locked="{{ $bloqueado ? '1' : '0' }}"
                                                    draggable="{{ $bloqueado ? 'false' : 'true' }}">
                                                    <td class="text-center text-muted">
                                                        <i class="bi bi-grip-vertical {{ $bloqueado ? 'opacity-25' : '' }}"></i>
                                                        <input type="hidden" name="orden[]" value="{{ $sorteo->id }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary js-orden-numero">{{ $index + 1 }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }}</div>
                                                        <small class="text-muted">{{ $sorteo->categoria->nombre ?? 'Sin categoria' }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($bloqueado)
                                                            <span class="badge bg-secondary">Bloqueado</span>
                                                        @else
                                                            <span class="badge bg-success">Movible</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
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

        .bracket-status {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 6px 10px;
            text-align: right;
        }

        .bracket-result {
            color: #212529;
            font-size: 12px;
            line-height: 1.25;
            margin-top: 4px;
            text-align: left;
        }

        .podio-horizontal {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
        }

        .podio-horizontal-item {
            border: 1px solid #d9e2ef;
            border-radius: 8px;
            padding: 12px;
            background: #ffffff;
        }

        .podio-horizontal-item span {
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            margin-right: 6px;
            font-weight: 900;
        }

        .podio-horizontal-item div {
            margin-top: 8px;
            font-weight: 800;
        }

        .podio-oro span {
            background: #ffd700;
            color: #111827;
        }

        .podio-plata span {
            background: #d1d5db;
            color: #111827;
        }

        .podio-bronce span {
            background: #cd7f32;
            color: #ffffff;
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

        .js-orden-row:not([data-locked="1"]) {
            cursor: grab;
        }

        .js-orden-row.dragging {
            opacity: .45;
        }

        .js-orden-row[data-locked="1"] {
            cursor: not-allowed;
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
                sortearButton.classList.add('btn-success');
                sortearButton.classList.remove('btn-primary');
                sortearButton.innerHTML = defaultButtonHtml;
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
                        categoriaOption.dataset.sorteada = categoriaItem.sorteada ? '1' : '0';
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

            function updateOrdenNumbers() {
                document.querySelectorAll('#sorteos-orden-list .js-orden-row').forEach(function (row, index) {
                    const badge = row.querySelector('.js-orden-numero');

                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }

            function closestOrdenRow(target) {
                return target.closest('#sorteos-orden-list .js-orden-row');
            }

            let draggedOrdenRow = null;

            document.addEventListener('dragstart', function (event) {
                const row = closestOrdenRow(event.target);

                if (!row) {
                    return;
                }

                if (row.dataset.locked === '1') {
                    event.preventDefault();
                    return;
                }

                draggedOrdenRow = row;
                row.classList.add('dragging');
                event.dataTransfer.effectAllowed = 'move';
            });

            document.addEventListener('dragover', function (event) {
                const row = closestOrdenRow(event.target);

                if (!row || !draggedOrdenRow || row === draggedOrdenRow || row.dataset.locked === '1') {
                    return;
                }

                event.preventDefault();

                const bounds = row.getBoundingClientRect();
                const insertAfter = event.clientY > bounds.top + (bounds.height / 2);

                row.parentNode.insertBefore(draggedOrdenRow, insertAfter ? row.nextSibling : row);
                updateOrdenNumbers();
            });

            document.addEventListener('dragend', function () {
                if (draggedOrdenRow) {
                    draggedOrdenRow.classList.remove('dragging');
                    draggedOrdenRow = null;
                }

                updateOrdenNumbers();
            });

            document.addEventListener('shown.bs.modal', function (event) {
                if (event.target.id === 'modal-orden-sorteos') {
                    updateOrdenNumbers();
                }
            });

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
