<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podio Kumite</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f4f6;
            min-height: 100vh;
        }

        .podio-page {
            padding: 28px;
        }

        .podio-header,
        .podio-card {
            background: #ffffff;
            border: 1px solid #d7dde5;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
        }

        .podio-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .podio-row {
            align-items: center;
            background: #ffffff;
            border: 1px solid #d7dde5;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
            display: flex;
            gap: 14px;
            min-height: 72px;
            padding: 14px 18px;
        }

        .podio-medalla {
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 44px;
            height: 44px;
            width: 44px;
            font-size: 1.15rem;
            font-weight: 900;
        }

        .oro .podio-medalla {
            background: #ffd700;
            color: #111827;
        }

        .plata .podio-medalla {
            background: #d1d5db;
            color: #111827;
        }

        .bronce .podio-medalla {
            background: #cd7f32;
            color: #ffffff;
        }

        .podio-texto {
            font-size: clamp(1.1rem, 2.2vw, 1.7rem);
            font-weight: 900;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <main class="podio-page">
        <div class="podio-header rounded p-4 mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="h2 mb-1">Podio Kumite</h1>
                <div class="text-muted">
                    {{ $sorteo->modalidad->nombre ?? 'Kumite' }} /
                    {{ $sorteo->categoria->nombre ?? 'Sin categoria' }}
                </div>
            </div>
            <div class="d-flex gap-2">
                @if ($siguienteSorteo)
                    <a href="{{ route('tablero.kumite', ['sorteo_id' => $siguienteSorteo->id]) }}" class="btn btn-success">
                        Siguiente categoria
                    </a>
                @endif
                <a href="{{ route('tablero.kumite', ['sorteo_id' => $sorteo->id]) }}" class="btn btn-primary">
                    Tablero
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                    Cerrar
                </a>
            </div>
        </div>

        <section class="podio-list">
            @if (! empty($podio['oro']))
                <article class="podio-row oro rounded">
                    <div class="podio-medalla">1</div>
                    <div class="podio-texto">1er Lugar - Oro - {{ $podio['oro'] }}</div>
                </article>
            @endif

            @if (! empty($podio['plata']))
                <article class="podio-row plata rounded">
                    <div class="podio-medalla">2</div>
                    <div class="podio-texto">2do Lugar - Plata - {{ $podio['plata'] }}</div>
                </article>
            @endif

            @if (! empty($podio['bronce_1']))
                <article class="podio-row bronce rounded">
                    <div class="podio-medalla">3</div>
                    <div class="podio-texto">3er Lugar - Bronce - {{ $podio['bronce_1'] }}</div>
                </article>
            @endif

            @if (! empty($podio['bronce_2']))
                <article class="podio-row bronce rounded">
                    <div class="podio-medalla">3</div>
                    <div class="podio-texto">3er Lugar - Bronce - {{ $podio['bronce_2'] }}</div>
                </article>
            @endif
        </section>
    </main>
</body>
</html>
