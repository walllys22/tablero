@extends('layouts.app')

@section('title', 'Imprimir competidores inscritos')

@section('content')
    @php
        $formatCategoriaNombre = function ($categoria, $modalidad) {
            return \App\Support\CategoriaNameFormatter::format($categoria, $modalidad->nombre);
        };

        $logo = $torneo->logo ? asset('storage/' . $torneo->logo) : asset('images/default.jpg');
        $user = auth()->user();
        $printedAt = now('America/La_Paz')->format('d/m/Y - H:i');
    @endphp

    <style>
        .print-actions {
            margin-bottom: 14px;
        }

        .inscripciones-print {
            background: #fff;
            color: #24292f;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            padding: 16px;
        }

        .print-header {
            align-items: center;
            background: #d9d9d9;
            border: 1px solid #bfbfbf;
            border-radius: 11px;
            display: grid;
            grid-template-columns: 92px 1fr 150px;
            gap: 12px;
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
            color: #000;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0;
            text-align: center;
            text-transform: uppercase;
        }

        .print-meta {
            color: #000;
            font-size: 10px;
            line-height: 1.35;
            text-align: left;
        }

        .print-page-number::after {
            content: "1";
        }

        .print-page-count::after {
            content: "1";
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

        .modalidad-block {
            margin-top: 16px;
            break-inside: avoid;
        }

        .modalidad-title {
            background: #eeeeee;
            border: 1px solid #cfcfcf;
            color: #000;
            font-weight: 700;
            padding: 8px 10px;
            text-transform: uppercase;
        }

        .categoria-title {
            background: #f3f4f6;
            border: 1px solid #dee2e6;
            border-top: 0;
            color: #000;
            font-weight: 700;
            padding: 8px 10px;
        }

        .competidores-table {
            border-collapse: collapse;
            width: 100%;
        }

        .competidores-table th,
        .competidores-table td {
            border: 1px solid #dee2e6;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        .competidores-table th {
            color: #343a40;
            font-weight: 700;
        }

        .empty-row {
            padding: 14px;
            text-align: center;
        }

        @media print {
            @page {
                margin: 8mm;
                size: letter portrait;
            }

            html,
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            body * {
                visibility: hidden !important;
            }

            .inscripciones-print,
            .inscripciones-print * {
                visibility: visible !important;
            }

            .inscripciones-print {
                font-size: 10px;
                left: 0;
                padding: 0 !important;
                position: absolute;
                top: 0;
                width: 100% !important;
            }

            .print-layout,
            .competidores-table {
                table-layout: fixed;
                width: 100% !important;
            }

            .modalidad-title,
            .categoria-title,
            .competidores-table th,
            .competidores-table td {
                overflow-wrap: anywhere;
                word-break: normal;
            }

            .print-header {
                display: grid !important;
                margin-bottom: 8px;
                visibility: visible !important;
            }

            .print-layout {
                display: table;
            }

            .print-layout thead {
                display: table-header-group;
            }

            .print-layout tbody {
                display: table-row-group;
            }

            .print-page-number::after {
                content: counter(page);
            }

            .print-page-count::after {
                content: counter(pages);
            }

            .modalidad-block {
                break-inside: auto;
                margin-top: 0 !important;
            }

            .modalidad-block + .modalidad-block {
                margin-top: 0 !important;
            }

            .modalidad-title,
            .categoria-title,
            .competidores-table th,
            .competidores-table td {
                padding: 5px 6px !important;
            }
        }
    </style>

    <div class="container-fluid pt-0 ps-0 pb-4">
        <div class="print-actions">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-dark">
                            <i class="bi bi-printer"></i> Imprimir competidores inscritos
                        </h1>
                        <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                    </div>

                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <a href="{{ route('inscripciones.index', $torneo) }}" class="btn btn-warning text-white">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="inscripciones-print">
            <table class="print-layout">
                <thead>
                    <tr>
                        <td>
                            <div class="print-header">
                                <div>
                                    <img src="{{ $logo }}" class="print-logo" alt="Logo torneo" onerror="this.style.visibility='hidden';">
                                </div>

                                <h2 class="print-title">
                                    TORNEO - {{ $torneo->nombre ?: 'Torneo sin nombre' }}<br>
                                    COMPETIDORES INSCRITOS
                                </h2>

                                <div class="print-meta">
                                    <div class="text-center">Pagina: <span class="print-page-number"></span> de <span class="print-page-count"></span></div>
                                    <div class="text-center">Impreso</div>
                                    <div class="text-center">{{ $printedAt }}</div>
                                    <div class="text-center">{{ $user->email ?? $user->name ?? 'Sistema' }}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            @forelse ($modalidades as $modalidadItem)
                                <div class="modalidad-block">
                                    <div class="modalidad-title">
                                        Modalidad: {{ $modalidadItem['modalidad']->nombre ?? 'Sin modalidad' }}
                                    </div>

                                    @foreach ($modalidadItem['categorias'] as $categoriaItem)
                                        <div class="categoria-title">
                                            Categoria: {{ $formatCategoriaNombre($categoriaItem['categoria'], $modalidadItem['modalidad']) }}
                                        </div>

                                        <table class="competidores-table">
                                            <thead>
                                                <tr>
                                                    <th>Competidor</th>
                                                    <th>Organizacion</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($categoriaItem['competidores'] as $detalle)
                                                    <tr>
                                                        <td>{{ $detalle->inscripcionCompetidor->persona->first_name ?? 'Sin competidor' }}</td>
                                                        <td>{{ $detalle->inscripcionCompetidor->inscripcionOrganizacion->organizacion->nombre ?? 'Sin organizacion' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endforeach
                                </div>
                            @empty
                                <div class="empty-row">No hay competidores inscritos</div>
                            @endforelse
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
@endsection
