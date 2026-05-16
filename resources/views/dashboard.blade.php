<x-app-layout>
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h1 class="h4 mb-0">
                    <i class="bi bi-award"></i>
                    {{ $torneo?->nombre ?? 'Sin torneo registrado' }}
                     -  Medallero de Kumite
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
                            @forelse ($medallero as $index => $fila)
                                @php
                                    $organizacion = $fila['organizacion'];
                                    $logo = $organizacion->logo
                                        ? asset('storage/' . ltrim($organizacion->logo, '/'))
                                        : asset('images/icono.png');
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="medallero-organizacion">
                                            <img src="{{ $logo }}" alt="{{ $organizacion->nombre }}"
                                                onerror="this.src='{{ asset('images/icono.png') }}'">
                                            <div>
                                                <div class="fw-bold">{{ $organizacion->nombre }}</div>
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
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay organizaciones participantes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
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
    </style>
</x-app-layout>
