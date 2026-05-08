<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero Kata</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        :root {
            --ao: #0d47a1;
            --ao-dark: #07356f;
            --aka: #b71c1c;
            --aka-dark: #7f1010;
            --panel: #ffffff;
            --page: #f3f4f6;
            --timer: #ffc107;
            --border: #d1d5db;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
            background: var(--page);
            font-family: Arial, Helvetica, sans-serif;
        }

        button {
            min-height: 42px;
            border-radius: 8px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
        }

        .kata-board {
            display: grid;
            grid-template-rows: minmax(0, 1fr) minmax(130px, 20vh);
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        .kata-main,
        .judges-panel {
            min-height: 0;
            overflow: hidden;
        }

        .kata-main {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) minmax(320px, 34vw) minmax(260px, 1fr);
            height: 100%;
        }

        .competitor-panel {
            display: flex;
            min-width: 0;
            height: 100%;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: #ffffff;
            text-align: center;
            border-color: #ffffff;
            border-style: solid;
        }

        .competitor-blue {
            background: var(--ao);
            border-width: 0 8px 0 0;
        }

        .competitor-red {
            background: var(--aka);
            border-width: 0 0 0 8px;
        }

        .competitor-state {
            display: block;
            width: 100%;
            font-size: clamp(1.4rem, 3vw, 3rem);
            font-weight: 900;
            letter-spacing: 0.08em;
            line-height: 1.1;
        }

        .competitor-name {
            display: block;
            width: 100%;
            margin-top: 12px;
            overflow: hidden;
            font-size: clamp(2rem, 4vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .timer-panel {
            display: flex;
            min-width: 0;
            height: 100%;
            flex-direction: column;
            justify-content: center;
            gap: 24px;
            padding: 18px;
            background: var(--panel);
        }

        .timer-display-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 0;
            padding: 44px 12px;
            border: 4px solid #e5e7eb;
            border-radius: 8px;
            background: var(--timer);
            box-shadow: inset 0 2px 12px rgba(15, 23, 42, 0.18);
            text-align: center;
        }

        #timer-display {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: clamp(4rem, 7vw, 8rem);
            font-weight: 900;
            line-height: 1;
        }

        .timer-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .judges-panel {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            align-items: center;
            gap: 12px;
            height: 100%;
            padding: 12px 18px;
            border-top: 8px solid var(--border);
            background: var(--panel);
        }

        .judge-box {
            min-width: 0;
        }

        .judge-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 8px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .score-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .score-input {
            width: 100%;
            min-width: 0;
            padding: 8px;
            border: 2px solid transparent;
            border-radius: 8px;
            color: #ffffff;
            font-size: clamp(1rem, 1.5vw, 1.25rem);
            font-weight: 900;
            text-align: center;
        }

        .score-blue {
            background: var(--ao);
            border-color: var(--ao-dark);
        }

        .score-red {
            background: var(--aka);
            border-color: var(--aka-dark);
        }

        .qr-modal {
            position: fixed;
            inset: 0;
            z-index: 1050;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.9);
        }

        .qr-card {
            display: flex;
            width: min(92vw, 430px);
            flex-direction: column;
            align-items: center;
            padding: 36px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        .qr-box {
            padding: 16px;
            border: 4px solid #f3f4f6;
            border-radius: 8px;
            background: #ffffff;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            margin: 0;
            -webkit-appearance: none;
        }

        input[type="number"] {
            -moz-appearance: textfield;
        }

        @media (max-width: 992px) {
            html,
            body {
                overflow: auto;
            }

            .kata-board {
                display: block;
                height: auto;
                min-height: 100vh;
                max-height: none;
                overflow: visible;
            }

            .kata-main {
                grid-template-columns: 1fr;
                height: auto;
                min-height: 100vh;
            }

            .competitor-panel {
                min-height: 220px;
                border-width: 0 0 6px;
            }

            .timer-panel {
                min-height: 360px;
            }

            .judges-panel {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                height: auto;
                overflow: visible;
            }
        }

        @media (max-width: 560px) {
            .judges-panel {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="kata-board">
        <main class="kata-main">
            <section class="competitor-panel competitor-blue">
                <span class="competitor-state">EN COMPETENCIA</span>
                <span class="competitor-name">-----*-----</span>
            </section>

            <section class="timer-panel">
                <div id="container-timer" class="timer-display-wrap">
                    <span id="timer-display">00:00</span>
                </div>

                <div class="timer-actions">
                    <button id="btn-inicio" onclick="iniciarTiempo(35)" class="btn btn-success btn-lg">Inicio</button>
                    <button id="btn-stop" onclick="detenerTiempo()" class="btn btn-warning btn-lg d-none">Stop</button>
                    <button id="btn-fin" onclick="iniciarTiempo(300)" class="btn btn-danger btn-lg d-none">Fin</button>
                    <button id="btn-cerrar" onclick="window.history.back()" class="btn btn-secondary btn-lg">Cerrar</button>
                </div>
            </section>

            <section class="competitor-panel competitor-red">
                <span class="competitor-state">EN COMPETENCIA</span>
                <span class="competitor-name">-----*-----</span>
            </section>
        </main>

        <section class="judges-panel">
            @for ($i = 1; $i <= 5; $i++)
                <div class="judge-box">
                    <div class="judge-title">
                        <span>Juez {{ $i }}</span>
                        <button onclick="generarQR({{ $i }})" class="btn btn-dark btn-sm" aria-label="Generar QR juez {{ $i }}">
                            <i class="fas fa-qrcode"></i>
                        </button>
                    </div>
                    <div class="score-row">
                        <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="score-input score-blue" placeholder="0.0">
                        <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="score-input score-red" placeholder="0.0">
                    </div>
                </div>
            @endfor
        </section>
    </div>

    <div id="modal-qr" class="qr-modal d-none">
        <div class="qr-card">
            <h2 id="qr-title" class="h3 fw-black text-uppercase fst-italic mb-4">Acceso Juez</h2>
            <div id="qrcode" class="qr-box"></div>
            <button onclick="cerrarModal()" class="btn btn-danger btn-lg fw-bold mt-4 px-5">Cerrar</button>
        </div>
    </div>

    <script>
        let timerInterval = null;
        let tiempoRestante = 0;
        let qrcodeObject = null;
        let etapaActual = null;

        document.addEventListener('DOMContentLoaded', function () {
            qrcodeObject = new QRCode(document.getElementById('qrcode'), {
                width: 300,
                height: 300,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            actualizarDisplay(0);
            mostrarBotones('inicio');
        });

        function iniciarTiempo(segundos) {
            clearInterval(timerInterval);
            timerInterval = null;
            tiempoRestante = segundos;
            etapaActual = segundos === 35 ? 'inicio' : 'fin';
            actualizarDisplay(tiempoRestante);
            mostrarBotones(etapaActual === 'inicio' ? 'conteo-inicial' : 'conteo-final');

            timerInterval = setInterval(function () {
                if (tiempoRestante <= 0) {
                    finalizarEtapa();
                    return;
                }

                tiempoRestante--;
                actualizarDisplay(tiempoRestante);
            }, 1000);
        }

        function finalizarEtapa() {
            clearInterval(timerInterval);
            timerInterval = null;

            if (etapaActual === 'inicio') {
                iniciarTiempo(300);
                return;
            }

            etapaActual = null;
            mostrarBotones('terminado');
        }

        function detenerTiempo() {
            clearInterval(timerInterval);
            timerInterval = null;
            mostrarBotones('terminado');
        }

        function actualizarDisplay(segundos) {
            const minutos = Math.floor(segundos / 60).toString().padStart(2, '0');
            const segundosRestantes = (segundos % 60).toString().padStart(2, '0');
            document.getElementById('timer-display').innerText = `${minutos}:${segundosRestantes}`;
        }

        function mostrarBotones(estado) {
            const btnInicio = document.getElementById('btn-inicio');
            const btnStop = document.getElementById('btn-stop');
            const btnFin = document.getElementById('btn-fin');
            const btnCerrar = document.getElementById('btn-cerrar');

            btnInicio.classList.toggle('d-none', estado !== 'inicio' && estado !== 'terminado');
            btnFin.classList.toggle('d-none', estado !== 'conteo-inicial');
            btnStop.classList.toggle('d-none', estado !== 'conteo-final');
            btnCerrar.classList.toggle('d-none', estado === 'conteo-inicial' || estado === 'conteo-final');
        }

        function generarQR(juezId) {
            if (!qrcodeObject) return;

            const modal = document.getElementById('modal-qr');
            const titulo = document.getElementById('qr-title');
            const urlAcceso = `${window.location.origin}/calificar/juez/${juezId}?t=${Date.now()}`;

            titulo.innerText = `JUEZ ${juezId}`;
            qrcodeObject.clear();
            qrcodeObject.makeCode(urlAcceso);
            modal.classList.remove('d-none');
        }

        function cerrarModal() {
            document.getElementById('modal-qr').classList.add('d-none');
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>
