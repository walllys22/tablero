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
                                                <span class="badge bg-danger mt-1">Realizada</span>
                                            @elseif ($sorteo->categoria_estado === 'ejecucion')
                                                <span class="badge bg-warning text-dark mt-1">En ejecucion</span>
                                            @else
                                                <span class="badge bg-primary mt-1">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($sorteo->area)
                                                Area {{ $sorteo->area }}
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
                                            @if (($sorteo->resultados_competencia_count ?? 0) > 0)
                                                @if ($sorteo->es_kata ?? false)
                                                    <a href="{{ route('sorteo-llaves.resultados', [$torneo, $sorteo]) }}" class="btn btn-sm btn-info text-white p-1" title="Resultados de Kata">
                                                        <i class="bi bi-clipboard-check"></i>
                                                    </a>
                                                    @if ($sorteo->categoria_completa)
                                                        <a href="{{ route('tablero.kata.podio', ['sorteo_id' => $sorteo->id]) }}" class="btn btn-sm btn-warning text-white p-1" title="Ver podio Kata">
                                                            <i class="bi bi-trophy"></i>
                                                        </a>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-secondary p-1" title="Podio no disponible" disabled>
                                                            <i class="bi bi-trophy"></i>
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('sorteo-llaves.resultados', [$torneo, $sorteo]) }}" class="btn btn-sm btn-info text-white p-1" title="Llaves realizadas">
                                                        <i class="bi bi-clipboard-check"></i>
                                                    </a>
                                                    <a href="{{ route('tablero.kumite.podio', ['sorteo_id' => $sorteo->id]) }}" class="btn btn-sm btn-warning text-white p-1" title="Ver podio">
                                                        <i class="bi bi-trophy"></i>
                                                    </a>
                                                @endif
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
                                            @if (($sorteo->categoria_estado ?? 'pendiente') === 'pendiente')
                                                <button type="button" class="btn btn-sm btn-danger p-1" title="Eliminar sorteo"
                                                    data-bs-toggle="modal" data-bs-target="#modal-delete-sorteo-{{ $sorteo->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
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
                                    Mostrando llaves sorteadas
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <div class="fw-semibold">{{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }}</div>
                                    <small class="text-muted">
                                        Numero {{ $sorteo->orden ?? $loop->iteration }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                                    </small>
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
                                @if (false)
                                    <div class="kata-results-panel">
                                        <div class="kata-results-title">Resultados de combate</div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle mb-0 kata-results-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 70px;">Llave</th>
                                                        <th>Rojo</th>
                                                        <th>Azul</th>
                                                        <th style="width: 120px;">Resultado</th>
                                                        <th>Detalle rojo</th>
                                                        <th>Detalle azul</th>
                                                        <th style="width: 180px;">Ganador</th>
                                                        <th style="width: 135px;">Fecha</th>
                                                        <th style="width: 105px;">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $resultadosPorIndice = ($sorteo->resultados_competencia ?? collect())->keyBy('indice_combate');
                                                        $kataRows = [];
                                                        $indiceCombateModal = 0;
                                                        $matchNumberModal = 1;

                                                        foreach (($sorteo->llaves ?? []) as $ronda) {
                                                            foreach (($ronda['combates'] ?? []) as $combate) {
                                                                $resultadoCombate = $resultadosPorIndice->get($indiceCombateModal);
                                                                $rojoNombre = $combate['a']['nombre'] ?? ($resultadoCombate ? ($resultadoCombate->competidor_rojo ?: '') : '');
                                                                $azulNombre = $combate['b']['nombre'] ?? ($resultadoCombate ? ($resultadoCombate->competidor_azul ?: '') : '');
                                                                $rojoOrganizacion = $combate['a']['organizacion'] ?? '';
                                                                $azulOrganizacion = $combate['b']['organizacion'] ?? '';
                                                                $tieneCompetidorRojo = trim((string) $rojoNombre) !== '';
                                                                $tieneCompetidorAzul = trim((string) $azulNombre) !== '';
                                                                $esByeReal = ($combate['bye'] ?? false) && ($tieneCompetidorRojo !== $tieneCompetidorAzul);

                                                                if (!(($sorteo->categoria_completa && ! $resultadoCombate) || $esByeReal)) {
                                                                    $ganadorVisible = '';

                                                                    if ($resultadoCombate && $resultadoCombate->ganador_color) {
                                                                        $ganadorVisible = $resultadoCombate->ganador_color === 'rojo' ? $rojoNombre : $azulNombre;
                                                                        $ganadorVisible = $ganadorVisible ?: ($resultadoCombate->ganador ?? '');
                                                                    }

                                                                    $kataRows[] = [
                                                                        'llave' => $matchNumberModal,
                                                                        'indice' => $indiceCombateModal,
                                                                        'rojo' => $rojoNombre ?: 'Competidor',
                                                                        'azul' => $azulNombre ?: 'Competidor',
                                                                        'rojo_org' => $rojoOrganizacion,
                                                                        'azul_org' => $azulOrganizacion,
                                                                        'resultado' => $resultadoCombate,
                                                                        'ganador_visible' => $ganadorVisible,
                                                                        'ganador_class' => $resultadoCombate && $resultadoCombate->ganador_color === 'rojo' ? 'bg-danger' : 'bg-primary',
                                                                        'fecha_tabla' => $resultadoCombate && $resultadoCombate->realizado_at ? $resultadoCombate->realizado_at->format('d/m/Y H:i') : '-',
                                                                        'fecha_input' => $resultadoCombate && $resultadoCombate->realizado_at ? $resultadoCombate->realizado_at->format('Y-m-d\\TH:i') : now()->format('Y-m-d\\TH:i'),
                                                                    ];
                                                                }

                                                                $indiceCombateModal++;
                                                                $matchNumberModal++;
                                                            }
                                                        }
                                                    @endphp
                                                    @foreach ($kataRows as $kataRow)
                                                        <tr>
                                                            <td class="text-center fw-bold">{{ $kataRow['llave'] }}</td>
                                                            <td>
                                                                <div class="kata-competidor-cell kata-competidor-rojo">
                                                                    <strong>{{ $kataRow['rojo'] }}</strong>
                                                                    {!! $kataRow['rojo_org'] ? '<small>' . e($kataRow['rojo_org']) . '</small>' : '' !!}
                                                                    {!! $kataRow['resultado'] && $kataRow['resultado']->kiken_rojo ? '<span class="badge bg-dark mt-1">Kiken</span>' : '' !!}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="kata-competidor-cell kata-competidor-azul">
                                                                    <strong>{{ $kataRow['azul'] }}</strong>
                                                                    {!! $kataRow['azul_org'] ? '<small>' . e($kataRow['azul_org']) . '</small>' : '' !!}
                                                                    {!! $kataRow['resultado'] && $kataRow['resultado']->kiken_azul ? '<span class="badge bg-dark mt-1">Kiken</span>' : '' !!}
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                {!! $kataRow['resultado'] ? '<span class="score-pill score-pill-red">' . e($kataRow['resultado']->puntaje_rojo) . '</span><span class="fw-bold mx-1">-</span><span class="score-pill score-pill-blue">' . e($kataRow['resultado']->puntaje_azul) . '</span>' : '<span class="badge bg-secondary">Pendiente</span>' !!}
                                                            </td>
                                                            <td>
                                                                {!! $kataRow['resultado'] ? '<div><strong>Kata Nro.:</strong> ' . e($kataRow['resultado']->kata_numero_rojo ?: '-') . '</div><div><strong>Nombre:</strong> ' . e($kataRow['resultado']->kata_nombre_rojo ?: '-') . '</div><div><strong>Kiken:</strong> ' . ($kataRow['resultado']->kiken_rojo ? 'Si' : 'No') . '</div>' : '<span class="text-muted">Sin resultado</span>' !!}
                                                            </td>
                                                            <td>
                                                                {!! $kataRow['resultado'] ? '<div><strong>Kata Nro.:</strong> ' . e($kataRow['resultado']->kata_numero_azul ?: '-') . '</div><div><strong>Nombre:</strong> ' . e($kataRow['resultado']->kata_nombre_azul ?: '-') . '</div><div><strong>Kiken:</strong> ' . ($kataRow['resultado']->kiken_azul ? 'Si' : 'No') . '</div>' : '<span class="text-muted">Sin resultado</span>' !!}
                                                            </td>
                                                            <td class="text-center">
                                                                {!! $kataRow['ganador_visible'] ? '<span class="badge ' . e($kataRow['ganador_class']) . ' kata-winner-badge">' . e($kataRow['ganador_visible']) . '</span>' : '<span class="text-muted">Pendiente</span>' !!}
                                                            </td>
                                                            <td class="text-center">{{ $kataRow['fecha_tabla'] }}</td>
                                                            <td class="text-center">
                                                                @if ($kataRow['resultado'])
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <button type="button" class="btn btn-sm btn-primary js-edit-kata-result"
                                                                            data-sorteo-id="{{ $sorteo->id }}"
                                                                            data-indice-combate="{{ $kataRow['indice'] }}"
                                                                            data-competidor-rojo="{{ $kataRow['rojo'] }}"
                                                                            data-competidor-azul="{{ $kataRow['azul'] }}"
                                                                            data-kata-numero-rojo="{{ $kataRow['resultado']->kata_numero_rojo ?? '' }}"
                                                                            data-kata-numero-azul="{{ $kataRow['resultado']->kata_numero_azul ?? '' }}"
                                                                            data-kata-nombre-rojo="{{ $kataRow['resultado']->kata_nombre_rojo ?? '' }}"
                                                                            data-kata-nombre-azul="{{ $kataRow['resultado']->kata_nombre_azul ?? '' }}"
                                                                            data-puntaje-rojo="{{ $kataRow['resultado']->puntaje_rojo ?? 0 }}"
                                                                            data-puntaje-azul="{{ $kataRow['resultado']->puntaje_azul ?? 0 }}"
                                                                            data-kiken-rojo="{{ $kataRow['resultado']->kiken_rojo ? '1' : '0' }}"
                                                                            data-kiken-azul="{{ $kataRow['resultado']->kiken_azul ? '1' : '0' }}"
                                                                            data-ganador="{{ $kataRow['resultado']->ganador ?? '' }}"
                                                                            data-ganador-color="{{ $kataRow['resultado']->ganador_color ?? '' }}"
                                                                            data-realizado-at="{{ $kataRow['fecha_input'] }}">
                                                                            <i class="bi bi-pencil-square"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-danger js-delete-kata-result"
                                                                            data-sorteo-id="{{ $sorteo->id }}"
                                                                            data-indice-combate="{{ $kataRow['indice'] }}"
                                                                            data-llave="{{ $kataRow['llave'] }}">
                                                                            <i class="bi bi-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                <div class="bracket-wrapper"
                                    data-update-url="{{ route('sorteo-llaves.competidores.update', [$torneo, $sorteo]) }}"
                                    data-editable="{{ in_array(($sorteo->categoria_estado ?? 'pendiente'), ['pendiente', 'ejecucion'], true) ? '1' : '0' }}">
                                    @php
                                        $resultadosPorIndice = ($sorteo->resultados_competencia ?? collect())->keyBy('indice_combate');
                                        $indiceCombateModal = 0;
                                        $matchNumbersModal = [];
                                        $nextMatchNumberModal = 1;

                                        foreach (($sorteo->llaves ?? []) as $numberRoundIndex => $numberRound) {
                                            foreach (($numberRound['combates'] ?? []) as $numberMatchIndex => $numberMatch) {
                                                $matchNumbersModal[$numberRoundIndex][$numberMatchIndex] = $nextMatchNumberModal++;
                                            }
                                        }

                                        $origenCompetidor = function ($llavesModal, $roundIndex, $matchIndex, $side) use (&$origenCompetidor) {
                                            $competidor = $llavesModal[$roundIndex]['combates'][$matchIndex][$side] ?? null;

                                            if (! $competidor) {
                                                return null;
                                            }

                                            if ($roundIndex === 0) {
                                                return [
                                                    'round_index' => 0,
                                                    'match_index' => $matchIndex,
                                                    'side' => $side,
                                                    'bye' => (bool) ($llavesModal[$roundIndex]['combates'][$matchIndex]['bye'] ?? false),
                                                ];
                                            }

                                            $combateActual = $llavesModal[$roundIndex]['combates'][$matchIndex] ?? [];
                                            $rondaActualNombre = mb_strtolower($llavesModal[$roundIndex]['nombre'] ?? '');
                                            $esSemifinalActualCompleta = str_contains($rondaActualNombre, 'semifinal')
                                                && ! empty($combateActual['a'])
                                                && ! empty($combateActual['b'])
                                                && ! ($combateActual['bye'] ?? false);

                                            if ($esSemifinalActualCompleta) {
                                                return [
                                                    'round_index' => $roundIndex,
                                                    'match_index' => $matchIndex,
                                                    'side' => $side,
                                                    'bye' => false,
                                                ];
                                            }

                                            $sourceIndex = ($matchIndex * 2) + ($side === 'a' ? 0 : 1);
                                            $sourceCombat = $llavesModal[$roundIndex - 1]['combates'][$sourceIndex] ?? null;

                                            if (! ($sourceCombat['bye'] ?? false)) {
                                                return null;
                                            }

                                            $sourceSide = ! empty($sourceCombat['a']) ? 'a' : (! empty($sourceCombat['b']) ? 'b' : null);

                                            return $sourceSide
                                                ? $origenCompetidor($llavesModal, $roundIndex - 1, $sourceIndex, $sourceSide)
                                                : null;
                                        };
                                        $slotModal = function ($llavesModal, $matchNumbers, $roundIndex, $matchIndex, $side, $combate) {
                                            $competidor = $combate[$side] ?? null;

                                            if ($competidor) {
                                                return [
                                                    'nombre' => $competidor['nombre'] ?? 'Competidor',
                                                    'organizacion' => $competidor['organizacion'] ?? '',
                                                ];
                                            }

                                            if ($roundIndex === 0) {
                                                return [
                                                    'nombre' => ($combate['bye'] ?? false) ? 'BYE' : 'Competidor',
                                                    'organizacion' => '',
                                                ];
                                            }

                                            $sourceIndex = ($matchIndex * 2) + ($side === 'a' ? 0 : 1);
                                            $sourceNumber = $matchNumbers[$roundIndex - 1][$sourceIndex] ?? null;

                                            return [
                                                'nombre' => $sourceNumber ? 'Ganador ' . $sourceNumber : 'Ganador',
                                                'organizacion' => '',
                                            ];
                                        };
                                    @endphp
                                    @foreach (($sorteo->llaves ?? []) as $roundIndex => $ronda)
                                        <div class="bracket-round">
                                            <div class="bracket-round-title">{{ $ronda['nombre'] ?? 'Ronda' }}</div>
                                            @foreach (($ronda['combates'] ?? []) as $matchIndex => $combate)
                                                @php
                                                    $resultadoCombate = $resultadosPorIndice->get($indiceCombateModal);
                                                    $llaveRealizada = (bool) $resultadoCombate;
                                                    $llavesModal = $sorteo->llaves ?? [];
                                                    $matchNumberModal = $matchNumbersModal[$roundIndex][$matchIndex] ?? $indiceCombateModal + 1;
                                                    $redSlotModal = $slotModal($llavesModal, $matchNumbersModal, $roundIndex, $matchIndex, 'a', $combate);
                                                    $blueSlotModal = $slotModal($llavesModal, $matchNumbersModal, $roundIndex, $matchIndex, 'b', $combate);
                                                    $rondaNombre = mb_strtolower($ronda['nombre'] ?? '');
                                                    $esSemifinalModal = str_contains($rondaNombre, 'semifinal');
                                                    $semifinalCompletaSinBye = $esSemifinalModal
                                                        && ! empty($combate['a'])
                                                        && ! empty($combate['b'])
                                                        && ! ($combate['bye'] ?? false);
                                                    $esRondaEditableBase = $roundIndex === 0
                                                        || $semifinalCompletaSinBye
                                                        || (! $esSemifinalModal && ! str_contains($rondaNombre, 'final'));
                                                    $puedeEditarCategoria = (($sorteo->categoria_estado ?? 'pendiente') === 'pendiente'
                                                            || ($semifinalCompletaSinBye && ! $llaveRealizada))
                                                        && (($ronda['sistema'] ?? null) !== 'round_robin');
                                                    $origenRojo = $origenCompetidor($llavesModal, $roundIndex, $matchIndex, 'a');
                                                    $origenAzul = $origenCompetidor($llavesModal, $roundIndex, $matchIndex, 'b');
                                                    $puedeMoverRojo = $puedeEditarCategoria
                                                        && ! empty($combate['a'])
                                                        && $origenRojo
                                                        && ($esRondaEditableBase || $origenRojo['bye']);
                                                    $puedeMoverAzul = $puedeEditarCategoria
                                                        && ! empty($combate['b'])
                                                        && $origenAzul
                                                        && ($esRondaEditableBase || $origenAzul['bye']);
                                                    $puedeRecibirRojo = $puedeEditarCategoria && ! empty($combate['a']) && $origenRojo;
                                                    $puedeRecibirAzul = $puedeEditarCategoria && ! empty($combate['b']) && $origenAzul;
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
                                                            @if ($sorteo->es_kata ?? false)
                                                                <div class="bracket-result">
                                                                    <strong>Kata rojo:</strong>
                                                                    {{ $resultadoCombate->kata_numero_rojo ?: '-' }}
                                                                    {{ $resultadoCombate->kata_nombre_rojo ?: '' }}
                                                                </div>
                                                                <div class="bracket-result">
                                                                    <strong>Kata azul:</strong>
                                                                    {{ $resultadoCombate->kata_numero_azul ?: '-' }}
                                                                    {{ $resultadoCombate->kata_nombre_azul ?: '' }}
                                                                </div>
                                                            @else
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
                                                            <div class="bracket-result">
                                                                <strong>Kiken:</strong>
                                                                Rojo {{ $resultadoCombate->kiken_rojo ? 'Si' : 'No' }}
                                                                -
                                                                Azul {{ $resultadoCombate->kiken_azul ? 'Si' : 'No' }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @php
                                                        $tieneCompetidorRojo = ! empty($combate['a']);
                                                        $tieneCompetidorAzul = ! empty($combate['b']);
                                                        $esByeReal = ($combate['bye'] ?? false) && ($tieneCompetidorRojo !== $tieneCompetidorAzul);
                                                        $byeCompetitorSide = $esByeReal
                                                            ? (! empty($combate['a']) ? 'a' : (! empty($combate['b']) ? 'b' : null))
                                                            : null;
                                                    @endphp

                                                    @if (! $byeCompetitorSide || $byeCompetitorSide === 'a')
                                                        <div class="bracket-player bracket-player-red {{ $puedeRecibirRojo ? 'js-bracket-player-slot' : '' }} {{ $puedeMoverRojo ? 'js-bracket-player-editable' : '' }}"
                                                            @if ($puedeRecibirRojo)
                                                                @if ($puedeMoverRojo)
                                                                    draggable="true"
                                                                @endif
                                                                data-round-index="{{ $roundIndex }}"
                                                                data-match-index="{{ $matchIndex }}"
                                                                data-side="a"
                                                                data-origin-round-index="{{ $origenRojo['round_index'] ?? 0 }}"
                                                                data-origin-match-index="{{ $origenRojo['match_index'] }}"
                                                                data-origin-side="{{ $origenRojo['side'] }}"
                                                                data-competidor-id="{{ $combate['a']['id'] }}"
                                                                data-player-name="{{ $combate['a']['nombre'] ?? '' }}"
                                                                data-player-org="{{ $combate['a']['organizacion'] ?? '' }}"
                                                            @endif>
                                                            <strong>{{ $redSlotModal['nombre'] }}</strong>
                                                            @if ($redSlotModal['organizacion'])
                                                                <small>{{ $redSlotModal['organizacion'] }}</small>
                                                            @endif
                                                            @if ($byeCompetitorSide === 'a')
                                                                <small class="d-block fw-bold">BYE</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if ($byeCompetitorSide === 'a' && $puedeEditarCategoria && $roundIndex === 0)
                                                        <div class="bracket-player bracket-player-blue bracket-player-bye-target js-bracket-player-slot"
                                                            data-round-index="{{ $roundIndex }}"
                                                            data-match-index="{{ $matchIndex }}"
                                                            data-side="b"
                                                            data-origin-round-index="{{ $roundIndex }}"
                                                            data-origin-match-index="{{ $matchIndex }}"
                                                            data-origin-side="b"
                                                            data-competidor-id="">
                                                            <strong>Soltar aqui</strong>
                                                            <small>Cambiar a azul</small>
                                                        </div>
                                                    @endif
                                                    @if ($byeCompetitorSide === 'b' && $puedeEditarCategoria && $roundIndex === 0)
                                                        <div class="bracket-player bracket-player-red bracket-player-bye-target js-bracket-player-slot"
                                                            data-round-index="{{ $roundIndex }}"
                                                            data-match-index="{{ $matchIndex }}"
                                                            data-side="a"
                                                            data-origin-round-index="{{ $roundIndex }}"
                                                            data-origin-match-index="{{ $matchIndex }}"
                                                            data-origin-side="a"
                                                            data-competidor-id="">
                                                            <strong>Soltar aqui</strong>
                                                            <small>Cambiar a rojo</small>
                                                        </div>
                                                    @endif
                                                    @if (! $byeCompetitorSide || $byeCompetitorSide === 'b')
                                                        <div class="bracket-player bracket-player-blue {{ $puedeRecibirAzul ? 'js-bracket-player-slot' : '' }} {{ $puedeMoverAzul ? 'js-bracket-player-editable' : '' }}"
                                                            @if ($puedeRecibirAzul)
                                                                @if ($puedeMoverAzul)
                                                                    draggable="true"
                                                                @endif
                                                                data-round-index="{{ $roundIndex }}"
                                                                data-match-index="{{ $matchIndex }}"
                                                                data-side="b"
                                                                data-origin-round-index="{{ $origenAzul['round_index'] ?? 0 }}"
                                                                data-origin-match-index="{{ $origenAzul['match_index'] }}"
                                                                data-origin-side="{{ $origenAzul['side'] }}"
                                                                data-competidor-id="{{ $combate['b']['id'] }}"
                                                                data-player-name="{{ $combate['b']['nombre'] ?? '' }}"
                                                                data-player-org="{{ $combate['b']['organizacion'] ?? '' }}"
                                                            @endif>
                                                            <strong>{{ $blueSlotModal['nombre'] }}</strong>
                                                            @if ($blueSlotModal['organizacion'])
                                                                <small>{{ $blueSlotModal['organizacion'] }}</small>
                                                            @endif
                                                            @if ($byeCompetitorSide === 'b')
                                                                <small class="d-block fw-bold">BYE</small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    <div class="bracket-match-number">{{ $matchNumberModal }}</div>
                                                </div>
                                                @php $indiceCombateModal++; @endphp
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-warning text-white js-cancel-bracket-changes" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success js-save-bracket-changes" disabled>
                                    <i class="bi bi-check-lg"></i> Guardar cambios
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if (($sorteo->categoria_estado ?? 'pendiente') === 'pendiente')
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
                @endif
            @endforeach

            <div class="modal fade" id="modal-edit-kata-result" tabindex="-1" aria-labelledby="modalEditKataResultLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <form id="form-edit-kata-result" class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold" id="modalEditKataResultLabel">Editar llave Kata</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="sorteo_id">
                            <input type="hidden" name="indice_combate">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="edit-kata-side edit-kata-side-red">
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
                                    <div class="edit-kata-side edit-kata-side-blue">
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
                                    Arrastre las categorias pendientes para definir el orden de competencia. Las categorias en ejecucion o realizadas quedan bloqueadas.
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
                                                    $estadoOrden = $sorteo->categoria_estado ?? 'pendiente';
                                                    $bloqueado = $estadoOrden !== 'pendiente';
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
                                                            <span class="badge bg-secondary">{{ $estadoOrden === 'realizada' ? 'Realizada' : 'En ejecucion' }}</span>
                                                        @else
                                                            <span class="badge bg-success">Pendiente</span>
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

        .bracket-match-number {
            font-size: 14px;
            font-weight: 700;
            line-height: 1;
            padding: 7px 10px 9px;
            text-align: center;
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

        .kata-results-panel {
            border: 1px solid #d7dde5;
            border-radius: 6px;
            overflow: hidden;
        }

        .kata-results-title {
            background: #f8f9fa;
            border-bottom: 1px solid #d7dde5;
            font-weight: 800;
            padding: 12px 16px;
        }

        .kata-results-table {
            font-size: 15px;
        }

        .kata-results-table th {
            background: #ffffff;
            font-weight: 800;
            text-align: center;
        }

        .kata-results-table td {
            min-height: 68px;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .kata-competidor-cell {
            border-left: 6px solid transparent;
            padding-left: 10px;
        }

        .kata-competidor-cell strong,
        .kata-competidor-cell small {
            display: block;
        }

        .kata-competidor-cell small {
            color: #6c757d;
            margin-top: 3px;
        }

        .kata-competidor-rojo {
            border-left-color: #dc3545;
        }

        .kata-competidor-azul {
            border-left-color: #0d6efd;
        }

        .score-pill {
            border-radius: 7px;
            color: #ffffff;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            line-height: 1;
            min-width: 28px;
            padding: 7px 8px;
        }

        .score-pill-red {
            background: #dc3545;
        }

        .score-pill-blue {
            background: #0d6efd;
        }

        .kata-winner-badge {
            max-width: 160px;
            overflow-wrap: anywhere;
            padding: 8px 10px;
            white-space: normal;
        }

        .edit-kata-side {
            border: 1px solid #d9e2ef;
            border-left: 7px solid transparent;
            border-radius: 6px;
            padding: 14px;
        }

        .edit-kata-side-red {
            border-left-color: #dc3545;
        }

        .edit-kata-side-blue {
            border-left-color: #0d6efd;
        }

        .bracket-player {
            min-height: 58px;
            padding: 8px 10px 8px 20px;
            border-bottom: 1px solid #e9ecef;
            position: relative;
        }

        .bracket-player:last-child {
            border-bottom: 0;
        }

        .bracket-player::before {
            bottom: 0;
            content: "";
            left: 0;
            position: absolute;
            top: 0;
            width: 8px;
        }

        .bracket-player-red::before {
            background: #dc3545;
        }

        .bracket-player-blue::before {
            background: #0d6efd;
        }

        .js-bracket-player-editable {
            cursor: grab;
        }

        .js-bracket-player-slot.is-selected {
            background: #fff3cd;
            outline: 2px solid #ffc107;
            outline-offset: -3px;
        }

        .js-bracket-player-editable.dragging strong,
        .js-bracket-player-editable.dragging small {
            opacity: .45;
        }

        .bracket-drag-preview {
            background: #ffffff;
            border: 1px solid #d9e2ef;
            border-radius: 6px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .18);
            color: #212529;
            left: -9999px;
            max-width: 220px;
            padding: 8px 10px;
            position: fixed;
            top: -9999px;
            z-index: 9999;
        }

        .bracket-drag-preview strong,
        .bracket-drag-preview small {
            display: block;
        }

        .js-bracket-player-slot.drag-over {
            outline: 2px dashed #198754;
            outline-offset: -4px;
        }

        .bracket-player-bye-target {
            display: none;
            opacity: .68;
        }

        .bracket-wrapper.is-bracket-dragging .bracket-player-bye-target {
            display: block;
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
            const guardarCombateKataUrl = @json(route('tablero.kata.combates.store'));
            const eliminarCombateKataUrl = @json(route('tablero.kata.combates.destroy'));
            const editKataModalElement = document.getElementById('modal-edit-kata-result');
            const editKataForm = document.getElementById('form-edit-kata-result');
            const editKataError = document.getElementById('edit-kata-error');
            const editKataModal = editKataModalElement && window.bootstrap
                ? new bootstrap.Modal(editKataModalElement)
                : null;
            let kataParentModal = null;

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

            function fillEditKataForm(button) {
                if (!editKataForm) {
                    return;
                }

                editKataForm.reset();
                editKataError?.classList.add('d-none');
                if (editKataError) {
                    editKataError.textContent = '';
                }

                const fields = {
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
                };

                Object.entries(fields).forEach(function ([name, value]) {
                    setFormValue(editKataForm, name, value);
                });

                actualizarGanadorKataEditado(false);
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

            function filterCategorias() {
                const modalidadId = modalidad.value;
                let selectedVisible = !categoria.value;

                categoria.querySelectorAll('option').forEach(function (option) {
                    const optionModalidadId = option.dataset.modalidadId;
                    const sorteada = option.dataset.sorteada === '1';
                    const visible = !option.value || (!sorteada && (!modalidadId || String(optionModalidadId) === String(modalidadId)));

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

                    item.categorias
                        .filter(function (categoriaItem) {
                            return !categoriaItem.sorteada;
                        })
                        .forEach(function (categoriaItem) {
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
                            initSorteosListControls('last');
                        }
                    });
            }

            function initSorteosListControls(initialPage = 1) {
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
                let currentPage = initialPage;

                function render() {
                    const search = (searchInput?.value || '').trim().toLowerCase();
                    const pageSize = parseInt(pageSizeSelect?.value || '10', 10);
                    const filteredRows = rows.filter(function (row) {
                        return !search || (row.dataset.search || '').includes(search);
                    });
                    const totalPages = Math.max(1, Math.ceil(filteredRows.length / pageSize));

                    if (currentPage === 'last') {
                        currentPage = totalPages;
                    }

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

            function closestBracketPlayer(target) {
                return target.closest('.js-bracket-player-slot');
            }

            function bracketSlotKey(player) {
                return `${player.dataset.originRoundIndex || player.dataset.roundIndex || 0}:${player.dataset.originMatchIndex || player.dataset.matchIndex}:${player.dataset.originSide || player.dataset.side}`;
            }

            function bracketSlots(wrapper) {
                const slotsByOrigin = new Map();
                const dirtyRoundIndex = wrapper.dataset.dirtyRoundIndex || '';

                Array.from(wrapper.querySelectorAll('.js-bracket-player-slot')).forEach(function (player) {
                    const originRoundIndex = player.dataset.originRoundIndex || player.dataset.roundIndex || 0;

                    if (dirtyRoundIndex !== '' && String(originRoundIndex) !== dirtyRoundIndex) {
                        return;
                    }

                    const originMatchIndex = player.dataset.originMatchIndex || player.dataset.matchIndex;
                    const originSide = player.dataset.originSide || player.dataset.side;
                    const origin = `${originRoundIndex}:${originMatchIndex}:${originSide}`;

                    if (slotsByOrigin.has(origin)) {
                        return;
                    }

                    const competidorId = player.dataset.competidorId;

                    slotsByOrigin.set(origin, {
                        round_index: parseInt(originRoundIndex || '0', 10),
                        match_index: parseInt(originMatchIndex, 10),
                        side: originSide,
                        competidor_id: competidorId ? parseInt(competidorId, 10) : null
                    });
                });

                return Array.from(slotsByOrigin.values());
            }

            function slotData(player) {
                return {
                    competidorId: player.dataset.competidorId || '',
                    name: player.dataset.playerName || player.querySelector('strong')?.textContent?.trim() || '',
                    org: player.dataset.playerOrg || player.querySelector('small:not(.d-block)')?.textContent?.trim() || ''
                };
            }

            function renderBracketSlot(player, data) {
                player.dataset.competidorId = data.competidorId || '';
                player.dataset.playerName = data.name || '';
                player.dataset.playerOrg = data.org || '';

                const color = player.dataset.side === 'a' ? 'rojo' : 'azul';
                const strong = player.querySelector('strong') || player.appendChild(document.createElement('strong'));
                let small = player.querySelector('small:not(.d-block)');

                if (!small) {
                    small = document.createElement('small');
                    player.appendChild(small);
                }

                strong.textContent = data.name || 'Soltar aqui';
                small.textContent = data.name ? data.org : `Cambiar a ${color}`;
                player.classList.toggle('js-bracket-player-editable', Boolean(data.competidorId));
                if (data.competidorId) {
                    player.setAttribute('draggable', 'true');
                } else {
                    player.removeAttribute('draggable');
                }
            }

            function markBracketDirty(wrapper, roundIndex = null) {
                const saveButton = wrapper.closest('.modal')?.querySelector('.js-save-bracket-changes');

                wrapper.dataset.dirty = '1';
                if (roundIndex !== null) {
                    wrapper.dataset.dirtyRoundIndex = String(roundIndex);
                }
                if (saveButton) {
                    saveButton.disabled = false;
                }
            }

            function saveBracketMove(draggedPlayer, targetPlayer) {
                const wrapper = targetPlayer.closest('.bracket-wrapper');
                const draggedOrigin = bracketSlotKey(draggedPlayer);
                const targetOrigin = bracketSlotKey(targetPlayer);

                if (!wrapper || wrapper.dataset.editable !== '1' || draggedPlayer === targetPlayer || draggedOrigin === targetOrigin) {
                    return;
                }

                const draggedData = slotData(draggedPlayer);
                const targetData = slotData(targetPlayer);
                const dirtyRoundIndex = draggedPlayer.dataset.originRoundIndex || draggedPlayer.dataset.roundIndex || 0;

                renderBracketSlot(draggedPlayer, targetData);
                renderBracketSlot(targetPlayer, draggedData);
                markBracketDirty(wrapper, dirtyRoundIndex);
            }

            function clearSelectedBracketPlayers(scope = document) {
                scope.querySelectorAll('.js-bracket-player-slot.is-selected').forEach(function (player) {
                    player.classList.remove('is-selected');
                });
            }

            function persistBracketChanges(wrapper, button) {
                const originalHtml = button.innerHTML;

                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Guardando...';

                fetch(wrapper.dataset.updateUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        slots: bracketSlots(wrapper)
                    })
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok) {
                                throw new Error(data.message || 'No se pudo mover el competidor.');
                            }

                            return data;
                        });
                    })
                    .then(function () {
                        wrapper.dataset.dirty = '0';
                        const modal = wrapper.closest('.modal');
                        const sorteoId = modal?.id?.replace('modal-llaves-sorteadas-', '') || '';

                        if (sorteoId) {
                            localStorage.setItem(`sorteo-llaves-updated-${sorteoId}`, String(Date.now()));
                        }

                        bootstrap.Modal.getOrCreateInstance(wrapper.closest('.modal')).hide();
                        return refreshSorteosList();
                    })
                    .catch(function (error) {
                        alert(error.message || 'No se pudo mover el competidor.');
                    })
                    .finally(function () {
                        button.innerHTML = originalHtml;
                        button.disabled = wrapper.dataset.dirty !== '1';
                    });
            }

            let draggedOrdenRow = null;
            let draggedBracketPlayer = null;
            let bracketDragPreview = null;
            let selectedBracketPlayer = null;

            document.addEventListener('dragstart', function (event) {
                const bracketPlayer = closestBracketPlayer(event.target);

                if (bracketPlayer && bracketPlayer.classList.contains('js-bracket-player-editable')) {
                    draggedBracketPlayer = bracketPlayer;
                    bracketPlayer.classList.add('dragging');
                    bracketPlayer.closest('.bracket-wrapper')?.classList.add('is-bracket-dragging');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', bracketPlayer.querySelector('strong')?.textContent || '');

                    bracketDragPreview = document.createElement('div');
                    bracketDragPreview.className = 'bracket-drag-preview';
                    bracketDragPreview.innerHTML = bracketPlayer.innerHTML;
                    bracketDragPreview.querySelectorAll('.d-block').forEach(function (item) {
                        item.classList.remove('d-block', 'fw-bold');
                    });
                    document.body.appendChild(bracketDragPreview);
                    event.dataTransfer.setDragImage(bracketDragPreview, 12, 12);

                    return;
                }

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
                const bracketPlayer = closestBracketPlayer(event.target);

                if (bracketPlayer && draggedBracketPlayer && bracketPlayer !== draggedBracketPlayer) {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                    bracketPlayer.classList.add('drag-over');
                    return;
                }

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

            document.addEventListener('dragleave', function (event) {
                const bracketPlayer = closestBracketPlayer(event.target);

                if (bracketPlayer) {
                    bracketPlayer.classList.remove('drag-over');
                }
            });

            document.addEventListener('drop', function (event) {
                const bracketPlayer = closestBracketPlayer(event.target);

                if (!bracketPlayer || !draggedBracketPlayer) {
                    return;
                }

                event.preventDefault();
                bracketPlayer.classList.remove('drag-over');
                saveBracketMove(draggedBracketPlayer, bracketPlayer);
            });

            document.addEventListener('click', function (event) {
                const bracketPlayer = closestBracketPlayer(event.target);

                if (!bracketPlayer || !bracketPlayer.closest('.bracket-wrapper') || bracketPlayer.closest('.bracket-wrapper').dataset.editable !== '1') {
                    return;
                }

                const wrapper = bracketPlayer.closest('.bracket-wrapper');

                if (!selectedBracketPlayer) {
                    if (!bracketPlayer.classList.contains('js-bracket-player-editable')) {
                        return;
                    }

                    selectedBracketPlayer = bracketPlayer;
                    clearSelectedBracketPlayers(wrapper);
                    bracketPlayer.classList.add('is-selected');
                    return;
                }

                if (selectedBracketPlayer === bracketPlayer) {
                    bracketPlayer.classList.remove('is-selected');
                    selectedBracketPlayer = null;
                    return;
                }

                saveBracketMove(selectedBracketPlayer, bracketPlayer);
                clearSelectedBracketPlayers(wrapper);
                selectedBracketPlayer = null;
            });

            document.addEventListener('dragend', function () {
                if (draggedBracketPlayer) {
                    draggedBracketPlayer.closest('.bracket-wrapper')?.classList.remove('is-bracket-dragging');
                    draggedBracketPlayer.classList.remove('dragging');
                    document.querySelectorAll('.js-bracket-player-slot.drag-over').forEach(function (player) {
                        player.classList.remove('drag-over');
                    });
                    draggedBracketPlayer = null;
                }

                if (bracketDragPreview) {
                    bracketDragPreview.remove();
                    bracketDragPreview = null;
                }

                if (draggedOrdenRow) {
                    draggedOrdenRow.classList.remove('dragging');
                    draggedOrdenRow = null;
                }

                selectedBracketPlayer = null;
                clearSelectedBracketPlayers();

                updateOrdenNumbers();
            });

            document.addEventListener('shown.bs.modal', function (event) {
                if (event.target.id === 'modal-orden-sorteos') {
                    updateOrdenNumbers();
                }

                if (event.target.id?.startsWith('modal-llaves-sorteadas-')) {
                    const wrapper = event.target.querySelector('.bracket-wrapper');
                    const saveButton = event.target.querySelector('.js-save-bracket-changes');

                    if (wrapper) {
                        wrapper.dataset.originalHtml = wrapper.innerHTML;
                        wrapper.dataset.dirty = '0';
                        wrapper.dataset.dirtyRoundIndex = '';
                    }

                    if (saveButton) {
                        saveButton.disabled = true;
                    }
                }
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-cancel-bracket-changes');

                if (!button) {
                    return;
                }

                const modal = button.closest('.modal');
                const wrapper = modal?.querySelector('.bracket-wrapper');

                if (wrapper?.dataset.originalHtml) {
                    wrapper.innerHTML = wrapper.dataset.originalHtml;
                    wrapper.dataset.dirty = '0';
                    wrapper.dataset.dirtyRoundIndex = '';
                }
            });

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-save-bracket-changes');

                if (!button) {
                    return;
                }

                const wrapper = button.closest('.modal')?.querySelector('.bracket-wrapper');

                if (!wrapper || wrapper.dataset.dirty !== '1') {
                    return;
                }

                persistBracketChanges(wrapper, button);
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

            document.addEventListener('click', function (event) {
                const button = event.target.closest('.js-edit-kata-result');

                if (!button) {
                    return;
                }

                fillEditKataForm(button);
                kataParentModal = button.closest('.modal');

                if (kataParentModal) {
                    bootstrap.Modal.getOrCreateInstance(kataParentModal).hide();
                }

                if (editKataModal) {
                    editKataModal.show();
                }
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

            editKataModalElement?.addEventListener('hidden.bs.modal', function () {
                if (!kataParentModal) {
                    return;
                }

                bootstrap.Modal.getOrCreateInstance(kataParentModal).show();
                kataParentModal = null;
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
                        kataParentModal = null;
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

            form.addEventListener('submit', function (event) {
                event.preventDefault();

                if (!modalidad.value || !categoria.value) {
                    filterCategorias();
                    return;
                }

                const url = `${form.action}?${new URLSearchParams(new FormData(form)).toString()}`;

                setSortingState(true);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            if (!response.ok) {
                                throw new Error(data.message || 'No se pudo sortear la categoria.');
                            }

                            return data;
                        });
                    })
                    .then(function () {
                        return Promise.all([
                            refreshCategorias(null),
                            refreshSorteosList()
                        ]);
                    })
                    .catch(function (error) {
                        sortearButton.innerHTML = defaultButtonHtml;
                        filterCategorias();
                        alert(error.message || 'No se pudo sortear la categoria.');
                    });
            });

            modalidad.addEventListener('change', filterCategorias);
            categoria.addEventListener('change', filterCategorias);
            filterCategorias();
            initSorteosListControls();
        });
    </script>
@endpush
