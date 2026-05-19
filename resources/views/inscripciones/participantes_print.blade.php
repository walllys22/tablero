@extends('layouts.app')

@section('title', 'Imprimir competidores inscritos')

@section('content')
    @php
        $logo = $torneo->logo ? asset('storage/' . $torneo->logo) : asset('images/default.jpg');
        $user = auth()->user();
        $printedAt = now('America/La_Paz')->format('d/m/Y - H:i');
    @endphp

    <style>
        .print-actions {
            margin-bottom: 14px;
        }

        .participantes-print {
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
            text-align: center;
        }

        .print-page-number::after {
            content: "1";
        }

        .print-page-count::after {
            content: "1";
        }

        .print-subtitle {
            color: #111827;
            font-size: 14px;
            font-weight: 700;
            margin: 14px 0 8px;
            text-transform: uppercase;
        }

        .participantes-table {
            border-collapse: collapse;
            width: 100%;
        }

        .participantes-table th,
        .participantes-table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
            vertical-align: middle;
        }

        .participantes-table th {
            color: #343a40;
            font-weight: 700;
        }

        .participantes-table .total-cell {
            text-align: center;
            width: 120px;
        }

        .label-print {
            border-radius: 999px;
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            padding: 7px 10px;
            white-space: nowrap;
        }

        .label-info-print {
            background: #c9f2fb;
            color: #14606f;
        }

        .label-success-print {
            background: #cfe9dc;
            color: #216947;
        }

        .modalidad-line {
            margin-bottom: 4px;
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

            .participantes-print,
            .participantes-print * {
                visibility: visible !important;
            }

            .participantes-print {
                font-size: 10px;
                left: 0;
                padding: 0 !important;
                position: absolute;
                top: 0;
                width: 100% !important;
            }

            .print-header {
                display: grid !important;
                margin-bottom: 8px;
            }

            .print-page-number::after {
                content: counter(page);
            }

            .print-page-count::after {
                content: counter(pages);
            }

            .participantes-table {
                table-layout: fixed;
                width: 100% !important;
            }

            .participantes-table th,
            .participantes-table td {
                padding: 6px !important;
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
                        <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }} / {{ $inscripcion->organizacion->nombre }}</small>
                    </div>

                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <a href="{{ route('inscripciones.participantes', [$torneo, $inscripcion, 'modalidad_id' => request('modalidad_id'), 'categoria_id' => request('categoria_id')]) }}" class="btn btn-warning text-white">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="participantes-print">
            <div class="print-header">
                <div>
                    <img src="{{ $logo }}" class="print-logo" alt="Logo torneo" onerror="this.style.visibility='hidden';">
                </div>

                <h2 class="print-title">
                    TORNEO - {{ $torneo->nombre ?: 'Torneo sin nombre' }}<br>
                    COMPETIDORES INSCRITOS
                </h2>

                <div class="print-meta">
                    <div>Pagina: <span class="print-page-number"></span> de <span class="print-page-count"></span></div>
                    <div>Impreso</div>
                    <div>{{ $printedAt }}</div>
                    <div>{{ $user->email ?? $user->name ?? 'Sistema' }}</div>
                </div>
            </div>

            <div class="print-subtitle">{{ $inscripcion->organizacion->nombre }}</div>

            <table class="participantes-table">
                <thead>
                    <tr>
                        <th>Competidor</th>
                        <th>Modalidades</th>
                        <th class="total-cell">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($competidores as $competidor)
                        @php
                            $competidorPeso = $pesosCompetidores->get($competidor->persona_id);
                            $competidorTieneKumite = $competidor->modalidades->contains(function ($detalle) {
                                return str_contains(mb_strtolower((string) optional($detalle->modalidad)->nombre), 'kumite');
                            });
                        @endphp
                        <tr>
                            <td>
                                {{ $competidor->persona->first_name }}{{ $competidor->persona->birth_date ? ' - ' . $competidor->persona->birth_date->diffInYears(now()) . ' años' : '' }}
                                @if ($competidorTieneKumite && $competidorPeso !== null)
                                    / {{ number_format((float) $competidorPeso, 3) }} Kg
                                @endif
                            </td>
                            <td>
                                @foreach ($competidor->modalidades as $detalle)
                                    <div class="modalidad-line">
                                        {{ $detalle->modalidad->nombre }}
                                        @if ($detalle->categoria)
                                            <small class="text-muted">/ {{ $detalle->categoria->nombre }}</small>
                                        @endif
                                        <span class="label-print label-info-print">{{ number_format((float) $detalle->costo, 2) }}</span>
                                    </div>
                                @endforeach
                            </td>
                            <td class="total-cell">
                                <span class="label-print label-success-print">{{ number_format((float) $competidor->total, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="empty-row">No hay competidores inscritos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
