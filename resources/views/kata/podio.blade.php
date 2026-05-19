<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podio Kata</title>
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
        .podio-row {
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
            display: flex;
            gap: 14px;
            min-height: 72px;
            padding: 14px 18px;
        }

        .podio-medalla {
            align-items: center;
            border-radius: 999px;
            display: inline-flex;
            flex: 0 0 44px;
            font-size: 1.15rem;
            font-weight: 900;
            height: 44px;
            justify-content: center;
            width: 44px;
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
                <h1 class="h2 mb-1">Podio Kata</h1>
                <div class="text-muted">
                    {{ $modalidad ?: 'Kata Individual' }} /
                    {{ $categoria ?: 'Sin categoria' }}
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('tablero.kata', array_filter(['sorteo_id' => $sorteo->id ?? null])) }}" class="btn btn-primary">Tablero</a>
                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">Cerrar</a>
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
