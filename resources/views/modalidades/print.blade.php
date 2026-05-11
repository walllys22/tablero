@extends('layouts.app')

@section('title', 'Imprimir modalidades')

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

        .modalidades-print {
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
            letter-spacing: 0;
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

        .modalidades-table {
            border-collapse: collapse;
            margin-top: 14px;
            width: 100%;
        }

        .modalidades-table th,
        .modalidades-table td {
            border: 1px solid #dee2e6;
            padding: 10px 7px;
            text-align: left;
            vertical-align: middle;
        }

        .modalidades-table th {
            color: #343a40;
            font-weight: 700;
        }

        .modalidades-table .modalidad-col {
            width: 30%;
        }

        .modalidades-table .modalidad-name {
            font-weight: 700;
        }

        .modalidad-name {
            font-weight: 700;
            color: #24292f;
        }

        .categoria-line {
            line-height: 1.55;
        }

        .categoria-line {
            line-height: 1.55;
        }

        .empty-row {
            text-align: center;
        }

        @media print {
            @page {
                margin: 16px;
                size: auto;
            }

            body {
                background: #fff !important;
            }

            .print-actions,
            .sidebar,
            .navbar,
            .footer,
            header {
                display: none !important;
            }

            .content,
            .container,
            .container-fluid {
                margin: 0 !important;
                max-width: none !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .modalidades-print {
                padding: 0;
            }

            .collapse:not(.show) {
                display: block !important;
            }

            .modalidad-toggle .bi-chevron-down {
                display: none !important;
            }

            .print-header {
                break-inside: avoid;
            }
        }
    </style>

    <div class="container-fluid pt-0 ps-0 pb-4">
        <div class="print-actions">
            <div class="card shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-dark">
                            <i class="bi bi-printer"></i> Imprimir modalidades
                        </h1>
                        <small class="text-muted">{{ $torneo->nombre ?: 'Torneo sin nombre' }}</small>
                    </div>

                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <a href="{{ route('modalidades.index', ['torneo' => $torneo, 'return' => request('return')]) }}" class="btn btn-warning text-white">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="modalidades-print">
            <div class="print-header">
                <div>
                    <img src="{{ $logo }}" class="print-logo" alt="Logo torneo" onerror="this.style.visibility='hidden';">
                </div>

                <h2 class="print-title">
                    TORNEO - {{ $torneo->nombre ?: 'Torneo sin nombre' }}<br>
                    MODALIDADES&nbsp;&nbsp;-&nbsp;&nbsp;CATEGORIAS
                </h2>

                <div class="print-meta">
                    <div class="text-center">Pagina: 1 de 1</div>
                    <div class="text-center">Impreso</div>
                    <div class="text-center">{{ $printedAt }}</div>
                    <div class="text-center">{{ $user->email ?? $user->name ?? 'Sistema' }}</div>
                </div>
            </div>

            <table class="modalidades-table">
                <thead>
                    <tr>
                        <th class="modalidad-col">Modalidad</th>
                        <th>Categorias</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($modalidades as $modalidad)
                        <tr>
                            <td class="modalidad-name">
                                {{ $modalidad->nombre }}
                            </td>
                            <td>
                                <div>
                                    @forelse ($modalidad->categorias as $categoria)
                                        <div class="categoria-line">
                                            <strong>{{ $formatCategoriaNombre($categoria, $modalidad) }}</strong>
                                        </div>
                                    @empty
                                        <span>Sin categorias</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="empty-row">No hay modalidades registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
