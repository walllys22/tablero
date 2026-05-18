<x-app-layout>
    @php
        $logoTorneo = $torneo?->logo ? asset('storage/' . ltrim($torneo->logo, '/')) : asset('images/default.jpg');
        $usuarioImpresion = auth()->user();
        $fechaImpresion = now('America/La_Paz')->format('d/m/Y - H:i');
        $titulosImpresion = [
            'todos' => 'MEDALLEROS',
            'general' => 'MEDALLERO GENERAL',
            'kumite_resumen' => 'MEDALLERO KUMITE',
            'kumite_detallado' => 'MEDALLERO KUMITE',
            'kata_resumen' => 'MEDALLERO KATA',
            'kata_detallado' => 'MEDALLERO KATA',
        ];
        $tipoBase = str_starts_with($tipo, 'kumite') ? 'kumite' : (str_starts_with($tipo, 'kata') ? 'kata' : $tipo);
        $imprimirDetallado = in_array($tipo, ['kumite_detallado', 'kata_detallado'], true);
        $tablas = collect([
            [
                'titulo' => 'Medallero General',
                'subtitulo' => 'Kata + Kumite',
                'filas' => $medalleroGeneral ?? collect(),
                'tipo' => 'general',
                'vacio' => 'No hay medallas registradas en Kata ni Kumite.',
            ],
            [
                'titulo' => 'Medallero de Kumite',
                'subtitulo' => null,
                'filas' => $medallero,
                'tipo' => 'kumite',
                'vacio' => 'No hay organizaciones participantes.',
            ],
            [
                'titulo' => 'Medallero de Kata',
                'subtitulo' => null,
                'filas' => $medalleroKata ?? collect(),
                'tipo' => 'kata',
                'vacio' => 'No hay medallas registradas en Kata.',
            ],
        ])->filter(fn ($tabla) => $tipo === 'todos' || $tabla['tipo'] === $tipoBase);
    @endphp

    <div class="container-fluid py-4 medallero-print-page">
        <div class="print-actions mb-3">
            <div class="card shadow-sm">
                <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h1 class="h4 mb-1">
                            <i class="bi bi-printer"></i>
                            Vista de impresion - {{ ucfirst($tipo) }}
                        </h1>
                        <small class="text-muted">{{ $torneo?->nombre ?: 'Torneo sin nombre' }}</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="imprimirMedallero()">
                            <i class="bi bi-printer"></i>
                            Imprimir
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                            <i class="bi bi-arrow-left"></i>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="print-document">
            <table class="print-layout">
                <thead>
                    <tr>
                        <td>
                            <div class="print-header">
                                <div>
                                    <img src="{{ $logoTorneo }}" class="print-logo" alt="Logo torneo" onerror="this.style.visibility='hidden';">
                                </div>

                                <h2 class="print-title">
                                    TORNEO - {{ $torneo?->nombre ?: 'Torneo sin nombre' }}<br>
                                    {{ $titulosImpresion[$tipo] ?? 'MEDALLEROS' }}
                                </h2>

                                <div class="print-meta">
                                    <div class="text-center">Pagina: <span class="print-page-number">1</span> de <span class="print-page-count">1</span></div>
                                    <div class="text-center">Impreso</div>
                                    <div class="text-center">{{ $fechaImpresion }}</div>
                                    <div class="text-center">{{ $usuarioImpresion->email ?? $usuarioImpresion->name ?? 'Sistema' }}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @foreach ($tablas as $tablaMedallero)
                                <article class="print-medallero-block">
                                    <h3 class="print-section-title">
                                        <i class="bi bi-award"></i>
                                        {{ $torneo?->nombre ?? 'Sin torneo registrado' }} - {{ $tablaMedallero['titulo'] }}
                                        @if ($tablaMedallero['subtitulo'])
                                            <span>({{ $tablaMedallero['subtitulo'] }})</span>
                                        @endif
                                    </h3>

                                    <table class="print-medallero-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 55px;">#</th>
                                                <th>Organizacion</th>
                                                <th style="width: 90px;">Oro</th>
                                                <th style="width: 90px;">Plata</th>
                                                <th style="width: 90px;">Bronce</th>
                                                <th style="width: 90px;">Total</th>
                                            </tr>
                                        </thead>
                        <tbody>
                            @forelse ($tablaMedallero['filas'] as $index => $fila)
                                                @php
                                                    $organizacion = $fila['organizacion'];
                                                    $logo = $organizacion->logo
                                                        ? asset('storage/' . ltrim($organizacion->logo, '/'))
                                                        : asset('images/icono.png');
                                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                                    <td>
                                                        <div class="print-org">
                                                            <img src="{{ $logo }}" alt="{{ $organizacion->nombre }}" onerror="this.src='{{ asset('images/icono.png') }}'">
                                                            <div>
                                                                <div class="fw-bold">{{ $organizacion->nombre }}</div>
                                                                <small>Estilo: {{ $organizacion->estilo->nombre ?? 'No asignado' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="print-badge print-badge-oro">{{ $fila['oro'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="print-badge print-badge-plata">{{ $fila['plata'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="print-badge print-badge-bronce">{{ $fila['bronce'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="print-badge print-badge-total">{{ $fila['total'] }}</span>
                                                    </td>
                                </tr>
                                @if ($imprimirDetallado && in_array($tablaMedallero['tipo'], ['kumite', 'kata'], true) && $fila['podios']->isNotEmpty())
                                    <tr class="print-detail-row">
                                        <td colspan="6">
                                            <div class="print-detail-title">Podios por categoria</div>
                                            <table class="print-detail-table">
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
                                                                <span class="print-label">Categoria</span>
                                                                <strong>{{ $podio['categoria'] }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="print-label">Modalidad</span>
                                                                {{ $podio['modalidad'] }}
                                                            </td>
                                                            <td>
                                                                <span class="print-medal print-medal-{{ $podio['medalla'] }}">
                                                                    {{ ucfirst($podio['medalla']) }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-bold">{{ $podio['competidor'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">{{ $tablaMedallero['vacio'] }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </article>
                            @endforeach
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>

    <style>
        .print-document {
            background: #ffffff;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            padding: 16px;
        }

        .print-pages {
            display: none;
        }

        .print-layout {
            border-collapse: collapse;
            width: 100%;
        }

        .print-layout > thead > tr > td,
        .print-layout > tbody > tr > td {
            border: 0;
            padding: 0;
        }

        .print-header {
            align-items: center;
            background: #d9d9d9;
            border: 1px solid #bfbfbf;
            border-radius: 11px;
            display: grid;
            gap: 12px;
            grid-template-columns: 92px 1fr 150px;
            min-height: 84px;
            padding: 7px 12px;
        }

        .print-logo {
            filter: grayscale(100%);
            height: 70px;
            object-fit: cover;
            width: 70px;
        }

        .print-title {
            color: #000000;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0;
            text-align: center;
            text-transform: uppercase;
        }

        .print-meta {
            color: #000000;
            font-size: 10px;
            line-height: 1.35;
        }

        .print-medallero-block {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            margin-top: 16px;
            overflow: hidden;
        }

        .print-section-title {
            align-items: center;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            gap: 8px;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            padding: 10px 14px;
        }

        .print-section-title span {
            color: #495057;
            font-size: 16px;
            font-weight: 400;
        }

        .print-medallero-table {
            border-collapse: collapse;
            width: 100%;
        }

        .print-medallero-table th,
        .print-medallero-table td {
            border: 1px solid #dee2e6;
            padding: 10px 12px;
            vertical-align: middle;
        }

        .print-medallero-table th {
            background: #ffffff;
            font-size: 16px;
            font-weight: 700;
            text-align: center;
        }

        .print-org {
            align-items: center;
            display: flex;
            gap: 10px;
        }

        .print-org img {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .12);
            height: 58px;
            object-fit: contain;
            padding: 3px;
            width: 58px;
        }

        .print-badge {
            border-radius: 999px;
            display: inline-block;
            font-weight: 700;
            min-width: 36px;
            padding: 6px 10px;
            text-align: center;
        }

        .print-badge-oro {
            background: #fff3cd;
            color: #8a6500;
        }

        .print-badge-plata {
            background: #e9ecef;
            color: #495057;
        }

        .print-badge-bronce {
            background: #f1d4bd;
            color: #7a3f12;
        }

        .print-badge-total {
            background: #cff4fc;
            color: #055160;
        }

        .print-detail-row > td {
            background: #f8fafc;
            padding: 18px 20px;
        }

        .print-detail-title {
            color: #334155;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .print-detail-table {
            background: #ffffff;
            border-collapse: collapse;
            width: 100%;
        }

        .print-detail-table th,
        .print-detail-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .print-detail-table th {
            background: #eef2f6;
            color: #334155;
            font-size: 11px;
            text-transform: uppercase;
        }

        .print-label {
            color: #6c757d;
            display: block;
            font-size: 11px;
        }

        .print-medal {
            border-radius: 999px;
            display: inline-block;
            font-weight: 700;
            min-width: 52px;
            padding: 5px 10px;
            text-align: center;
        }

        .print-medal-oro {
            background: #fff3cd;
            color: #8a6500;
        }

        .print-medal-plata {
            background: #e9ecef;
            color: #495057;
        }

        .print-medal-bronce {
            background: #f1d4bd;
            color: #7a3f12;
        }

        @media print {
            @page {
                margin: 8mm;
                size: letter portrait;
            }

            body {
                background: #ffffff !important;
            }

            .print-actions,
            .print-document,
            .bottom-bar,
            .app-footer,
            footer,
            nav,
            aside,
            header,
            .sidebar,
            .navbar {
                display: none !important;
            }

            .container-fluid {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .print-document {
                padding: 0 !important;
            }

            .print-header {
                break-inside: avoid;
                margin-bottom: 10px;
            }

            .print-layout {
                display: table;
                width: 100% !important;
            }

            .print-layout thead {
                display: table-header-group;
            }

            .print-layout tbody {
                display: table-row-group;
            }

            .print-medallero-block {
                margin-top: 12px;
            }

            .print-pages {
                display: block !important;
            }

            .print-page {
                background: #ffffff;
                color: #000000;
                font-family: Arial, Helvetica, sans-serif;
            }

            .print-page:not(:last-child) {
                break-after: page;
                page-break-after: always;
            }
        }
    </style>

    <script>
        function crearPaginaImpresion(contenedor, numero, total) {
            const pagina = document.createElement('section');
            pagina.className = 'print-page';

            const header = document.querySelector('.print-header').cloneNode(true);
            header.querySelector('.print-page-number').textContent = numero;
            header.querySelector('.print-page-count').textContent = total;
            pagina.appendChild(header);

            const contenido = document.createElement('div');
            contenido.className = 'print-page-content';
            pagina.appendChild(contenido);
            contenedor.appendChild(pagina);

            return { pagina, contenido };
        }

        function crearBloqueVacioDesde(origen) {
            const bloque = document.createElement('article');
            bloque.className = 'print-medallero-block';
            bloque.appendChild(origen.querySelector('.print-section-title').cloneNode(true));

            const tabla = document.createElement('table');
            tabla.className = 'print-medallero-table';
            tabla.appendChild(origen.querySelector('.print-medallero-table > thead').cloneNode(true));
            tabla.appendChild(document.createElement('tbody'));
            bloque.appendChild(tabla);

            return bloque;
        }

        function altoPaginaDisponible() {
            return 900;
        }

        function paginaExcede(pagina) {
            return pagina.scrollHeight > altoPaginaDisponible();
        }

        function paginarMedallero() {
            const anterior = document.querySelector('.print-pages');

            if (anterior) {
                anterior.remove();
            }

            const paginas = document.createElement('div');
            paginas.className = 'print-pages';
            paginas.style.cssText = 'display:block; position:absolute; left:-99999px; top:0; visibility:hidden; width:100%;';
            document.querySelector('.medallero-print-page').appendChild(paginas);

            let paginaActual = crearPaginaImpresion(paginas, 1, 1);
            let bloqueActual = null;
            let tbodyActual = null;

            document.querySelectorAll('.print-medallero-block').forEach(function (bloqueOrigen) {
                bloqueActual = crearBloqueVacioDesde(bloqueOrigen);
                paginaActual.contenido.appendChild(bloqueActual);
                tbodyActual = bloqueActual.querySelector('tbody');

                const filas = Array.from(bloqueOrigen.querySelectorAll('.print-medallero-table > tbody > tr'));

                for (let index = 0; index < filas.length; index++) {
                    const grupo = [filas[index].cloneNode(true)];

                    if (filas[index + 1]?.classList.contains('print-detail-row')) {
                        grupo.push(filas[index + 1].cloneNode(true));
                        index++;
                    }

                    grupo.forEach(function (fila) {
                        tbodyActual.appendChild(fila);
                    });

                    if (paginaExcede(paginaActual.pagina) && tbodyActual.children.length > grupo.length) {
                        grupo.forEach(function () {
                            tbodyActual.lastElementChild.remove();
                        });

                        paginaActual = crearPaginaImpresion(paginas, paginas.children.length + 1, 1);
                        bloqueActual = crearBloqueVacioDesde(bloqueOrigen);
                        paginaActual.contenido.appendChild(bloqueActual);
                        tbodyActual = bloqueActual.querySelector('tbody');

                        grupo.forEach(function (fila) {
                            tbodyActual.appendChild(fila);
                        });
                    }
                }
            });

            const total = paginas.children.length;
            paginas.querySelectorAll('.print-page').forEach(function (pagina, index) {
                pagina.querySelector('.print-page-number').textContent = String(index + 1);
                pagina.querySelector('.print-page-count').textContent = String(total);
            });
            paginas.removeAttribute('style');
        }

        function imprimirMedallero() {
            paginarMedallero();
            window.print();
        }

        window.addEventListener('load', paginarMedallero);
        window.addEventListener('beforeprint', paginarMedallero);
    </script>
</x-app-layout>
