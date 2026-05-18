<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Kata</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <style>
        :root {
            --aka: #ff0000;
            --ao: #0000ff;
            --amarillo: #ffc928;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            background: #f3f3f3;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
            height: 100%;
            margin: 0;
            overflow: hidden;
            width: 100%;
        }

        .resultado-screen {
            justify-items: center;
            display: grid;
            grid-template-rows: minmax(0, 1fr) clamp(78px, 15vh, 104px);
            height: 100vh;
            padding: clamp(8px, 2vh, 16px) clamp(10px, 2.2vw, 16px) 8px;
            width: 100vw;
        }

        .winner-card {
            background: {{ ($resultadoKata['color'] ?? 'rojo') === 'azul' ? 'var(--ao)' : 'var(--aka)' }};
            border: 2px solid rgba(255, 255, 255, .55);
            border-radius: 12px;
            display: grid;
            grid-template-rows: auto auto minmax(0, 1fr) clamp(92px, 24vh, 132px);
            min-height: 0;
            overflow: hidden;
            width: 60vw;
        }

        .winner-title {
            border-bottom: 2px solid rgba(255, 255, 255, .5);
            font-size: clamp(1.05rem, 2.65vw, 1.55rem);
            font-weight: 900;
            line-height: 1;
            padding: clamp(7px, 1.45vh, 10px) 16px;
            text-align: center;
        }

        .winner-title span {
            display: block;
            text-transform: uppercase;
        }

        .kata-info {
            border-bottom: 2px solid rgba(255, 255, 255, .5);
            font-size: clamp(1rem, 2.25vw, 1.35rem);
            font-weight: 900;
            line-height: 1.05;
            padding: clamp(10px, 2vh, 14px) clamp(13px, 2.4vw, 18px);
        }

        .score-row {
            align-items: center;
            border-bottom: 2px solid rgba(255, 255, 255, .5);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 0;
            padding: 0 clamp(32px, 7vw, 64px);
        }

        .score {
            font-size: clamp(5.8rem, 17vw, 8.6rem);
            font-weight: 900;
            line-height: .85;
        }

        .score-blue {
            text-align: right;
        }

        .flags-row {
            align-items: center;
            background: #ffffff;
            border-radius: 14px 14px 0 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 0;
            padding: clamp(8px, 2vh, 16px) clamp(20px, 5vw, 38px);
        }

        .flags {
            align-items: start;
            display: flex;
            gap: clamp(11px, 2.2vw, 18px);
            height: 100%;
        }

        .flags-blue {
            justify-content: end;
        }

        .flag {
            display: inline-block;
            height: clamp(50px, 11vh, 70px);
            position: relative;
            width: clamp(34px, 6.4vw, 48px);
        }

        .flag::before {
            background: #d39b59;
            content: "";
            height: 100%;
            left: 0;
            position: absolute;
            top: 8px;
            width: 2px;
        }

        .flag::after {
            border-radius: 3px;
            box-shadow: 4px 5px 7px rgba(0, 0, 0, .2);
            content: "";
            height: 62%;
            left: 3px;
            position: absolute;
            top: 0;
            transform: skewY(2deg);
            width: 92%;
        }

        .flag-red::after {
            background: var(--aka);
        }

        .flag-blue::after {
            background: var(--ao);
        }

        .footer {
            align-items: center;
            display: flex;
            justify-content: center;
            padding-top: clamp(10px, 3vh, 24px);
            width: 60vw;
        }

        .close-button {
            background: var(--amarillo);
            border: 0;
            border-radius: 10px;
            box-shadow: 0 5px 8px rgba(0, 0, 0, .24);
            color: #000000;
            cursor: pointer;
            font-family: inherit;
            font-size: clamp(.95rem, 1.9vw, 1.1rem);
            font-weight: 800;
            min-height: clamp(48px, 9vh, 52px);
            min-width: clamp(112px, 22vw, 114px);
            padding: 0 28px;
        }

        @media (max-width: 720px) {
            .resultado-screen {
                grid-template-rows: minmax(0, 1fr) auto;
            }

            .winner-card,
            .footer {
                width: 100%;
            }

            .footer {
                grid-template-columns: 1fr;
                justify-items: center;
            }
        }
    </style>
</head>
<body>
    <main class="resultado-screen">
        <section class="winner-card">
            <header class="winner-title">
                Ganador:
                <span>{{ strtoupper($resultadoKata['nombre']) }}</span>
            </header>

            <section class="kata-info">
                <div>Kata Nro. : {{ $resultadoKata['kata_numero'] }}</div>
                <div>Nombre Kata: {{ $resultadoKata['kata_nombre'] }}</div>
            </section>

            <section class="score-row" aria-label="Puntaje">
                <div class="score score-red">{{ $resultadoKata['banderas_rojas'] }}</div>
                <div class="score score-blue">{{ $resultadoKata['banderas_azules'] }}</div>
            </section>

            <section class="flags-row">
                <div class="flags flags-red" aria-label="Banderas rojas">
                    @for ($index = 0; $index < $resultadoKata['banderas_rojas']; $index++)
                        <span class="flag flag-red"></span>
                    @endfor
                </div>

                <div class="flags flags-blue" aria-label="Banderas azules">
                    @for ($index = 0; $index < $resultadoKata['banderas_azules']; $index++)
                        <span class="flag flag-blue"></span>
                    @endfor
                </div>
            </section>
        </section>

        <footer class="footer">
            <button type="button" class="close-button" onclick="window.location.href='{{ route('tablero.kata') }}'">
                Cerrar
            </button>
        </footer>
    </main>
</body>
</html>
