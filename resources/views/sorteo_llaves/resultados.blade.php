@extends('layouts.app')

@section('title', 'Resultados de llaves')

@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h1 class="h3 mb-0 text-dark">
                        <i class="bi bi-clipboard-check"></i> Llaves realizadas
                    </h1>
                    <small class="text-muted">
                        {{ $torneo->nombre ?: 'Torneo sin nombre' }} /
                        {{ $sorteo->modalidad->nombre ?? 'Sin modalidad' }} /
                        {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                    </small>
                </div>
                <a href="{{ route('sorteo-llaves.index', $torneo) }}" class="btn btn-warning text-white">
                    <i class="bi bi-x-lg"></i> Cerrar
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold">Resultados de combate</div>
            <div class="card-body">
                @if ($sorteo->resultadosKumite->isEmpty())
                    <div class="alert alert-info mb-0">Todavia no hay llaves realizadas.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 90px; text-align: center;">Llave</th>
                                    <th>Rojo</th>
                                    <th>Azul</th>
                                    <th style="width: 120px; text-align: center;">Resultado</th>
                                    <th>Detalle rojo</th>
                                    <th>Detalle azul</th>
                                    <th style="width: 190px; text-align: center;">Ganador</th>
                                    <th style="width: 150px; text-align: center;">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sorteo->resultadosKumite as $resultado)
                                    @php
                                        $faltasRojo = collect($resultado->faltas_rojo ?? [])->implode(', ');
                                        $faltasAzul = collect($resultado->faltas_azul ?? [])->implode(', ');
                                        $tecnicasRojo = $resultado->tecnicas_rojo ?? [];
                                        $tecnicasAzul = $resultado->tecnicas_azul ?? [];
                                        $ganadorClass = $resultado->ganador_color === 'rojo' ? 'bg-danger' : 'bg-primary';
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold">{{ $resultado->numero_llave }}</td>
                                        <td>
                                            <strong>{{ $resultado->competidor_rojo ?: 'Sin competidor' }}</strong>
                                            @if ($resultado->senshu === 'rojo')
                                                <span class="badge bg-warning text-dark ms-1">Senshu</span>
                                            @endif
                                            @if ($resultado->kiken_rojo)
                                                <span class="badge bg-dark ms-1">Kiken</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $resultado->competidor_azul ?: 'Sin competidor' }}</strong>
                                            @if ($resultado->senshu === 'azul')
                                                <span class="badge bg-warning text-dark ms-1">Senshu</span>
                                            @endif
                                            @if ($resultado->kiken_azul)
                                                <span class="badge bg-dark ms-1">Kiken</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger">{{ $resultado->puntaje_rojo }}</span>
                                            <span class="mx-1">-</span>
                                            <span class="badge bg-primary">{{ $resultado->puntaje_azul }}</span>
                                        </td>
                                        <td>
                                            <div><strong>Tecnicas:</strong> Y {{ $tecnicasRojo['yuko'] ?? 0 }}, W {{ $tecnicasRojo['wazari'] ?? 0 }}, I {{ $tecnicasRojo['ippon'] ?? 0 }}</div>
                                            <div><strong>Faltas:</strong> {{ $faltasRojo ?: 'Sin faltas' }}</div>
                                            <div><strong>Kiken:</strong> {{ $resultado->kiken_rojo ? 'Si' : 'No' }}</div>
                                        </td>
                                        <td>
                                            <div><strong>Tecnicas:</strong> Y {{ $tecnicasAzul['yuko'] ?? 0 }}, W {{ $tecnicasAzul['wazari'] ?? 0 }}, I {{ $tecnicasAzul['ippon'] ?? 0 }}</div>
                                            <div><strong>Faltas:</strong> {{ $faltasAzul ?: 'Sin faltas' }}</div>
                                            <div><strong>Kiken:</strong> {{ $resultado->kiken_azul ? 'Si' : 'No' }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if ($resultado->ganador)
                                                <span class="badge {{ $ganadorClass }}">{{ $resultado->ganador }}</span>
                                            @else
                                                <span class="text-muted">Sin ganador</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $resultado->realizado_at ? $resultado->realizado_at->format('d/m/Y H:i') : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
