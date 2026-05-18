<x-app-layout>
    @php
        $logoTorneo = $torneo?->logo ? asset('storage/' . ltrim($torneo->logo, '/')) : asset('images/default.jpg');
        $usuarioImpresion = auth()->user();
        $fechaImpresion = now('America/La_Paz')->format('d/m/Y - H:i');
    @endphp

    <div class="container-fluid py-4">
        <div class="dashboard-print-header">
            <div>
                <img src="{{ $logoTorneo }}" class="dashboard-print-logo" alt="Logo torneo" onerror="this.style.visibility='hidden';">
            </div>

            <h2 class="dashboard-print-title">
                TORNEO - {{ $torneo?->nombre ?: 'Torneo sin nombre' }}<br>
                <span class="print-title-scope print-title-todos">MEDALLEROS</span>
                <span class="print-title-scope print-title-general">MEDALLERO GENERAL</span>
                <span class="print-title-scope print-title-kumite">MEDALLERO KUMITE</span>
                <span class="print-title-scope print-title-kata">MEDALLERO KATA</span>
            </h2>

            <div class="dashboard-print-meta">
                <div class="text-center">Pagina: <span class="print-page-number"></span> de <span class="print-page-count"></span></div>
                <div class="text-center">Impreso</div>
                <div class="text-center">{{ $fechaImpresion }}</div>
                <div class="text-center">{{ $usuarioImpresion->email ?? $usuarioImpresion->name ?? 'Sistema' }}</div>
            </div>
        </div>

        <div class="card shadow-sm mb-4 dashboard-print-controls">
            <div class="card-body d-flex flex-wrap align-items-end gap-3">
                <div>
                    <label for="print-medallero-scope" class="form-label fw-semibold mb-1">Medallero a imprimir</label>
                    <select id="print-medallero-scope" class="form-select" style="min-width: 180px;">
                        <option value="todos">Todos</option>
                        <option value="general">General</option>
                        <option value="kumite_resumen">Kumite Resumen</option>
                        <option value="kumite_detallado">Kumite Detallado</option>
                        <option value="kata_resumen">Kata Resumen</option>
                        <option value="kata_detallado">Kata Detallado</option>
                    </select>
                </div>
                <button type="button" id="btn-print-medallero" class="btn btn-primary">
                    <i class="bi bi-printer"></i>
                    Imprimir
                </button>
            </div>
        </div>

        @foreach ([
            [
                'titulo' => 'Medallero General',
                'subtitulo' => 'Kata + Kumite',
                'filas' => $medalleroGeneral ?? collect(),
                'prefijo' => 'general',
                'tipo' => 'general',
                'vacio' => 'No hay medallas registradas en Kata ni Kumite.',
            ],
            [
                'titulo' => 'Medallero de Kumite',
                'subtitulo' => null,
                'filas' => $medallero,
                'prefijo' => 'kumite',
                'tipo' => 'kumite',
                'vacio' => 'No hay organizaciones participantes.',
            ],
            [
                'titulo' => 'Medallero de Kata',
                'subtitulo' => null,
                'filas' => $medalleroKata ?? collect(),
                'prefijo' => 'kata',
                'tipo' => 'kata',
                'vacio' => 'No hay medallas registradas en Kata.',
            ],
        ] as $tablaMedallero)
        <div class="card shadow-sm medallero-card {{ ! $loop->last ? 'mb-4' : '' }}" data-medallero-tipo="{{ $tablaMedallero['tipo'] }}">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">
                    <i class="bi bi-award"></i>
                    {{ $torneo?->nombre ?? 'Sin torneo registrado' }}
                    - {{ $tablaMedallero['titulo'] }}
                    @if ($tablaMedallero['subtitulo'])
                        <span class="text-muted fs-6">({{ $tablaMedallero['subtitulo'] }})</span>
                    @endif
                </h1>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 medallero-table">
                        <thead>
                            <tr>
                                <th style="width: 70px; text-align: center;">#</th>
                                <th>Organización</th>
                                <th style="width: 110px; text-align: center;">Oro</th>
                                <th style="width: 110px; text-align: center;">Plata</th>
                                <th style="width: 110px; text-align: center;">Bronce</th>
                                <th style="width: 110px; text-align: center;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tablaMedallero['filas'] as $index => $fila)
                                @php
                                    $organizacion = $fila['organizacion'];
                                    $logo = $organizacion->logo
                                        ? asset('storage/' . ltrim($organizacion->logo, '/'))
                                        : asset('images/icono.png');
                                    $collapseId = 'podios-' . $tablaMedallero['prefijo'] . '-organizacion-' . $organizacion->id;
                                    $tienePodios = $fila['total'] > 0 && $fila['podios']->isNotEmpty();
                                @endphp
                                <tr class="{{ $tienePodios ? 'medallero-row-toggle' : '' }}"
                                    @if ($tienePodios)
                                        data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="false"
                                        aria-controls="{{ $collapseId }}"
                                    @endif
                                >
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="medallero-organizacion">
                                            <img src="{{ $logo }}" alt="{{ $organizacion->nombre }}"
                                                onerror="this.src='{{ asset('images/icono.png') }}'">
                                            <div>
                                                <div class="fw-bold d-flex align-items-center gap-2">
                                                    {{ $organizacion->nombre }}
                                                    @if ($tienePodios)
                                                        <i class="bi bi-chevron-down medallero-chevron"></i>
                                                    @endif
                                                </div>
                                                <small class="text-muted">
                                                    Estilo: {{ $organizacion->estilo->nombre ?? 'No asignado' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="medalla-badge medalla-oro">{{ $fila['oro'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="medalla-badge medalla-plata">{{ $fila['plata'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="medalla-badge medalla-bronce">{{ $fila['bronce'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="medalla-badge medalla-total">{{ $fila['total'] }}</span>
                                    </td>
                                </tr>
                                @if ($tienePodios)
                                    <tr class="collapse medallero-podios-row" id="{{ $collapseId }}">
                                        <td colspan="6">
                                            <div class="medallero-podios">
                                                @if ($tablaMedallero['tipo'] === 'general')
                                                    @php
                                                        $medallasPorModalidad = $fila['podios']
                                                            ->groupBy('modalidad')
                                                            ->map(function ($podiosModalidad, $modalidadNombre) {
                                                                return [
                                                                    'modalidad' => $modalidadNombre,
                                                                    'oro' => $podiosModalidad->where('medalla', 'oro')->count(),
                                                                    'plata' => $podiosModalidad->where('medalla', 'plata')->count(),
                                                                    'bronce' => $podiosModalidad->where('medalla', 'bronce')->count(),
                                                                    'total' => $podiosModalidad->count(),
                                                                ];
                                                            })
                                                            ->sortBy('modalidad');
                                                    @endphp
                                                    <div class="medallero-podios-title">Medallas por modalidad</div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Modalidad</th>
                                                                    <th style="width: 110px; text-align: center;">Oro</th>
                                                                    <th style="width: 110px; text-align: center;">Plata</th>
                                                                    <th style="width: 110px; text-align: center;">Bronce</th>
                                                                    <th style="width: 110px; text-align: center;">Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($medallasPorModalidad as $modalidad)
                                                                    <tr>
                                                                        <td class="fw-semibold">{{ $modalidad['modalidad'] }}</td>
                                                                        <td class="text-center">
                                                                            <span class="medalla-badge medalla-oro">{{ $modalidad['oro'] }}</span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="medalla-badge medalla-plata">{{ $modalidad['plata'] }}</span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="medalla-badge medalla-bronce">{{ $modalidad['bronce'] }}</span>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <span class="medalla-badge medalla-total">{{ $modalidad['total'] }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="medallero-podios-title">Podios por categoria</div>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm align-middle mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>Categoria</th>
                                                                    <th>Modalidad</th>
                                                                    <th>Medalla</th>
                                                                    <th>Competidor</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($fila['podios']->sortBy([['categoria', 'asc'], ['medalla', 'asc'], ['competidor', 'asc']]) as $podio)
                                                                    <tr>
                                                                        <td>
                                                                            <span class="text-muted small d-block">Categoria</span>
                                                                            <span class="fw-semibold">{{ $podio['categoria'] }}</span>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-muted small d-block">Modalidad</span>
                                                                            {{ $podio['modalidad'] }}
                                                                        </td>
                                                                        <td>
                                                                            <span class="medalla-badge medalla-{{ $podio['medalla'] }}">
                                                                                {{ ucfirst($podio['medalla']) }}
                                                                            </span>
                                                                        </td>
                                                                        <td class="fw-semibold">{{ $podio['competidor'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        {{ $tablaMedallero['vacio'] }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <style>
        .dashboard-print-header {
            display: none;
        }

        .dashboard-print-controls .btn {
            min-height: 38px;
        }

        .medallero-table th {
            text-align: center;
        }

        .medallero-organizacion {
            align-items: center;
            display: flex;
            gap: 12px;
        }

        .medallero-organizacion img {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .12);
            height: 58px;
            object-fit: contain;
            padding: 3px;
            width: 58px;
        }

        .medallero-row-toggle {
            cursor: pointer;
        }

        .medallero-row-toggle[aria-expanded="true"] .medallero-chevron {
            transform: rotate(180deg);
        }

        .medallero-chevron {
            color: #6c757d;
            font-size: .9rem;
            transition: transform .18s ease;
        }

        .medallero-podios-row > td {
            background: #f8fafc;
            padding: 0;
        }

        .medallero-podios {
            padding: 16px 20px;
        }

        .medallero-podios-title {
            color: #334155;
            font-size: .9rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .medallero-podios .table {
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .medallero-podios th {
            background: #f1f5f9;
            color: #334155;
            font-size: .82rem;
            text-transform: uppercase;
        }

        .medalla-badge {
            border-radius: 999px;
            display: inline-block;
            font-weight: 700;
            min-width: 36px;
            padding: 6px 10px;
        }

        .medalla-oro {
            background: #fff3cd;
            color: #8a6500;
        }

        .medalla-plata {
            background: #e9ecef;
            color: #495057;
        }

        .medalla-bronce {
            background: #f1d4bd;
            color: #7a3f12;
        }

        .medalla-total {
            background: #cff4fc;
            color: #055160;
        }

        @media print {
            @page {
                margin: 8mm;
                size: letter portrait;
            }

            body.print-medallero-general .medallero-card:not([data-medallero-tipo="general"]),
            body.print-medallero-kumite .medallero-card:not([data-medallero-tipo="kumite"]),
            body.print-medallero-kata .medallero-card:not([data-medallero-tipo="kata"]),
            .dashboard-print-controls,
            nav,
            aside,
            header,
            .sidebar,
            .navbar {
                display: none !important;
            }

            .dashboard-print-header {
                align-items: center;
                background: #d9d9d9 !important;
                border: 1px solid #bfbfbf;
                border-radius: 11px;
                color: #000000 !important;
                display: grid !important;
                gap: 12px;
                grid-template-columns: 92px 1fr 150px;
                margin-bottom: 12px;
                min-height: 84px;
                padding: 7px 12px;
            }

            .dashboard-print-logo {
                filter: grayscale(100%);
                height: 70px;
                object-fit: cover;
                width: 70px;
            }

            .dashboard-print-title {
                color: #000000 !important;
                font-size: 16px;
                font-weight: 700;
                line-height: 1.35;
                margin: 0;
                text-align: center;
                text-transform: uppercase;
            }

            .dashboard-print-meta {
                color: #000000 !important;
                font-size: 10px;
                line-height: 1.35;
                text-align: left;
            }

            .print-title-scope {
                display: none;
            }

            .print-title-todos,
            body.print-medallero-general .print-title-general,
            body.print-medallero-kumite .print-title-kumite,
            body.print-medallero-kata .print-title-kata {
                display: inline;
            }

            body.print-medallero-general .print-title-todos,
            body.print-medallero-kumite .print-title-todos,
            body.print-medallero-kata .print-title-todos {
                display: none;
            }

            .print-page-number::after {
                content: counter(page);
            }

            .print-page-count::after {
                content: counter(pages);
            }

            body,
            .container-fluid {
                background: #ffffff !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .card {
                border: 0 !important;
                box-shadow: none !important;
                break-inside: avoid;
            }

            .card-header {
                border-bottom: 1px solid #dee2e6 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .card-body {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .collapse {
                display: table-row !important;
            }
        }
    </style>

    <script>
        document.getElementById('btn-print-medallero').addEventListener('click', function () {
            const scope = document.getElementById('print-medallero-scope').value;
            const url = new URL('{{ route('dashboard.medallero.print') }}', window.location.origin);
            url.searchParams.set('tipo', scope);
            window.location.href = url.toString();
        });
    </script>
</x-app-layout>
