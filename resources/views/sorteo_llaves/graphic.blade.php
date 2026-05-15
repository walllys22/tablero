@extends('layouts.app')

@section('title', 'Ver llaves')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="bi bi-diagram-3"></i> Ver llaves
                    </h1>
                    <small class="text-muted">
                        {{ $torneo->nombre ?: 'Torneo sin nombre' }} /
                        {{ $categoria->modalidad->nombre }} /
                        {{ $categoria->nombre }}
                    </small>
                </div>
                <div>
                    <button type="button" class="btn btn-success me-2" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    <a href="{{ route('sorteo-llaves.index', [$torneo, 'modalidad_id' => request('modalidad_id'), 'categoria_id' => request('categoria_id'), 'sortear' => 1, 'seed' => request('seed')]) }}" class="btn btn-warning text-white">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>

        @if ($llaves)
            @php
                $matchNumbers = [];
                $nextNumber = 1;

                foreach ($llaves as $roundIndex => $ronda) {
                    foreach ($ronda['combates'] as $matchIndex => $combate) {
                        $matchNumbers[$roundIndex][$matchIndex] = $nextNumber++;
                    }
                }

                $matchHeight = 117;
                $baseGap = 18;

                $competidorTexto = function ($competidor) {
                    return [
                        'nombre' => $competidor['nombre'] ?? 'Competidor',
                        'organizacion' => $competidor['organizacion'] ?? '',
                    ];
                };

                $slotTexto = function ($roundIndex, $matchIndex, $side, $combate) use ($matchNumbers, $competidorTexto) {
                    if ($roundIndex === 0) {
                        $competidor = $side === 'a' ? $combate['a'] : $combate['b'];

                        if ($competidor) {
                            return $competidorTexto($competidor);
                        }

                        return [
                            'nombre' => $combate['bye'] ? 'BYE' : 'Competidor',
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

            <div class="graphic-sheet">
                <div class="graphic-header">
                    <strong>{{ $categoria->modalidad->nombre }}</strong>
                    <span>{{ $categoria->nombre }}</span>
                    <span>{{ $competidores->count() }} competidor(es)</span>
                </div>

                <div class="graphic-bracket">
                    @foreach ($llaves as $roundIndex => $ronda)
                        @php
                            $roundDistance = ($matchHeight + $baseGap) * (2 ** $roundIndex);
                            $roundGap = $roundDistance - $matchHeight;
                            $roundOffset = (($matchHeight + $baseGap) * ((2 ** $roundIndex) - 1)) / 2;
                        @endphp

                        <div class="graphic-round" style="--round-gap: {{ $roundGap }}px; --round-offset: {{ $roundOffset }}px;">
                            <div class="graphic-round-title">{{ $ronda['nombre'] }}</div>

                            @foreach ($ronda['combates'] as $matchIndex => $combate)
                                @php
                                    $redSlot = $slotTexto($roundIndex, $matchIndex, 'a', $combate);
                                    $blueSlot = $slotTexto($roundIndex, $matchIndex, 'b', $combate);
                                    $matchNumber = $matchNumbers[$roundIndex][$matchIndex];
                                    $matchPosition = $matchIndex % 2 === 0 ? 'match-top' : 'match-bottom';
                                @endphp

                                <div class="graphic-match {{ $roundIndex === 0 ? 'first-round' : '' }} {{ $matchPosition }}">
                                    <div class="graphic-slot red-slot">
                                        <strong>{{ $redSlot['nombre'] }}</strong>
                                        @if ($redSlot['organizacion'])
                                            <small>{{ $redSlot['organizacion'] }}</small>
                                        @endif
                                    </div>
                                    <div class="graphic-slot blue-slot">
                                        <strong>{{ $blueSlot['nombre'] }}</strong>
                                        @if ($blueSlot['organizacion'])
                                            <small>{{ $blueSlot['organizacion'] }}</small>
                                        @endif
                                    </div>
                                    <div class="graphic-match-number">{{ $matchNumber }}</div>
                                    <span class="graphic-pair-exit" aria-hidden="true"></span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="alert alert-warning">No hay suficientes competidores para generar llaves.</div>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .graphic-sheet {
            background: #fff;
            border: 1px solid #111;
            border-radius: 0;
            color: #111;
            overflow-x: auto;
            padding: 14px;
        }

        .graphic-header {
            align-items: center;
            border: 1px solid #111;
            display: flex;
            gap: 18px;
            justify-content: center;
            font-size: 12px;
            margin-bottom: 28px;
            padding: 6px 10px;
            text-transform: uppercase;
        }

        .graphic-bracket {
            align-items: flex-start;
            display: flex;
            gap: 86px;
            min-height: 560px;
            min-width: max-content;
            padding: 0 22px 42px 10px;
        }

        .graphic-round {
            min-width: 170px;
            padding-top: var(--round-offset);
        }

        .graphic-round-title {
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 14px;
            text-align: center;
            text-transform: uppercase;
        }

        .graphic-match {
            margin-bottom: var(--round-gap);
            position: relative;
            width: 170px;
        }

        .graphic-match::after {
            border-top: 1px solid #111;
            content: "";
            left: 100%;
            position: absolute;
            top: 50%;
            width: 43px;
        }

        .graphic-round:last-child .graphic-match::after {
            display: none;
        }

        .graphic-match::before {
            border-right: 1px solid #111;
            content: "";
            left: calc(100% + 43px);
            position: absolute;
            top: 50%;
            height: calc(117px + var(--round-gap));
        }

        .graphic-match.match-bottom::before,
        .graphic-round:last-child .graphic-match::before {
            display: none;
        }

        .graphic-pair-exit {
            border-top: 1px solid #111;
            content: "";
            left: calc(100% + 43px);
            position: absolute;
            top: calc(50% + ((117px + var(--round-gap)) / 2));
            width: 43px;
        }

        .graphic-match.match-bottom .graphic-pair-exit,
        .graphic-round:last-child .graphic-pair-exit {
            display: none;
        }

        .graphic-slot {
            border: 1px solid #111;
            height: 48px;
            overflow: hidden;
            padding: 8px 8px 8px 28px;
            position: relative;
            width: 100%;
        }

        .graphic-slot::before {
            bottom: 0;
            content: "";
            left: 0;
            position: absolute;
            top: 0;
            width: 24px;
        }

        .graphic-slot strong {
            display: block;
            font-size: 13px;
            line-height: 1.15;
        }

        .graphic-slot small {
            display: block;
            font-size: 10px;
            line-height: 1.2;
        }

        .red-slot {
            border-left: 1px solid #111;
        }

        .blue-slot {
            border-left: 1px solid #111;
            border-top: 0;
        }

        .red-slot::before {
            background: #f00;
        }

        .blue-slot::before {
            background: #0039d8;
        }

        .graphic-match-number {
            font-size: 14px;
            line-height: 1;
            margin-top: 7px;
            text-align: center;
        }

        .podium-row {
            display: flex;
            justify-content: space-around;
            margin-top: 18px;
            min-width: 760px;
        }

        .podium-box {
            border: 1px solid #111;
            font-weight: 700;
            padding: 10px 22px;
            text-align: center;
        }

        .podium-box.gold {
            background: #fff3cd;
        }

        .podium-box.silver {
            background: #e9ecef;
        }

        .podium-box.bronze {
            background: #f1d0b5;
        }

        @media print {
            @page {
                margin: 12px;
                size: landscape;
            }

            .print-actions,
            .sidebar,
            .navbar,
            .bottom-bar,
            .app-footer,
            header,
            .card.shadow-sm.mb-3 {
                display: none !important;
            }

            .container-fluid,
            .app-main {
                margin: 0 !important;
                padding: 0 !important;
                max-width: none !important;
            }

            .graphic-sheet {
                border: 0;
                overflow: visible;
                padding: 0;
            }
        }
    </style>
@endpush
