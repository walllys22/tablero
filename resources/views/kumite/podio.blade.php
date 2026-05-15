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

        .podio-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
        }

        .podio-card {
            min-height: 190px;
            padding: 22px 18px;
            text-align: center;
        }

        .podio-card .medalla {
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 64px;
            width: 64px;
            margin-bottom: 14px;
            font-size: 1.6rem;
            font-weight: 900;
        }

        .oro .medalla {
            background: #ffd700;
            color: #111827;
        }

        .plata .medalla {
            background: #d1d5db;
            color: #111827;
        }

        .bronce .medalla {
            background: #cd7f32;
            color: #ffffff;
        }

        .podio-card h2 {
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .podio-card strong {
            display: block;
            font-size: 1.35rem;
            line-height: 1.2;
            margin-top: 16px;
            min-height: 58px;
        }

        @media (max-width: 1000px) {
            .podio-grid {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }

        @media (max-width: 560px) {
            .podio-grid {
                grid-template-columns: 1fr;
            }
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
                <a href="{{ route('tablero.kumite', ['sorteo_id' => $sorteo->id]) }}" class="btn btn-primary">
                    Tablero
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-warning text-white">
                    Cerrar
                </a>
            </div>
        </div>

        <section class="podio-grid">
            <article class="podio-card oro rounded">
                <div class="medalla">1</div>
                <h2>1er lugar - Oro</h2>
                <strong>{{ $podio['oro'] ?: 'Pendiente' }}</strong>
            </article>

            <article class="podio-card plata rounded">
                <div class="medalla">2</div>
                <h2>2do lugar - Plata</h2>
                <strong>{{ $podio['plata'] ?: 'Pendiente' }}</strong>
            </article>

            <article class="podio-card bronce rounded">
                <div class="medalla">3</div>
                <h2>3er lugar - Bronce</h2>
                <strong>{{ $podio['bronce_1'] ?: 'Pendiente' }}</strong>
            </article>

            <article class="podio-card bronce rounded">
                <div class="medalla">3</div>
                <h2>3er lugar - Bronce</h2>
                <strong>{{ $podio['bronce_2'] ?: 'Pendiente' }}</strong>
            </article>
        </section>
    </main>
</body>
</html>
