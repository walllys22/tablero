<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero Kata</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body class="kata-board">
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
                <button id="btn-inicio" onclick="iniciarTiempo(35); toggleTimerButtons('inicio')" class="btn btn-success btn-lg fw-bold text-uppercase">Inicio</button>
                <button id="btn-stop" onclick="detenerTiempo()" class="btn btn-warning btn-lg fw-bold text-uppercase d-none">Stop</button>
                <button id="btn-fin" onclick="iniciarTiempo(300); toggleTimerButtons('fin')" class="btn btn-danger btn-lg fw-bold text-uppercase d-none">Fin</button>
                <button id="btn-cerrar" onclick="window.history.back()" class="btn btn-secondary btn-lg fw-bold text-uppercase">Cerrar</button>
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
                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                    <span class="fw-black text-uppercase">JUEZ {{ $i }}</span>
                    <button onclick="generarQR({{ $i }})" class="btn btn-dark btn-sm" aria-label="Generar QR juez {{ $i }}">
                        <i class="fas fa-qrcode"></i>
                    </button>
                </div>
                <div class="d-flex gap-2">
                    <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="score-input score-blue" placeholder="0.0">
                    <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="score-input score-red" placeholder="0.0">
                </div>
            </div>
        @endfor
    </section>

    <div id="modal-qr" class="qr-modal d-none">
        <div class="qr-card">
            <h2 id="qr-title" class="h3 fw-black text-uppercase fst-italic mb-4">Acceso Juez</h2>
            <div id="qrcode" class="qr-box"></div>
            <button onclick="cerrarModal()" class="btn btn-danger btn-lg fw-bold mt-4 px-5">CERRAR</button>
        </div>
    </div>

    <script>
        let timerInterval = null;
        let tiempoRestante = 0;
        let qrcodeObject = null;

        $(document).ready(function() {
            qrcodeObject = new QRCode(document.getElementById("qrcode"), {
                width: 300,
                height: 300,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        });

        function iniciarTiempo(segundos) {
            clearInterval(timerInterval);
            tiempoRestante = segundos;
            actualizarDisplay(tiempoRestante);

            timerInterval = setInterval(() => {
                if (tiempoRestante <= 0) {
                    clearInterval(timerInterval);

                    if (segundos === 35) {
                        toggleTimerButtons('fin');
                        iniciarTiempo(300);
                    } else {
                        document.getElementById('btn-cerrar').classList.remove('d-none');
                    }
                    return;
                }
                tiempoRestante--;
                actualizarDisplay(tiempoRestante);
            }, 1000);
        }

        function detenerTiempo() {
            clearInterval(timerInterval);
            document.getElementById('btn-cerrar').classList.remove('d-none');
        }

        function actualizarDisplay(segundos) {
            const min = Math.floor(segundos / 60).toString().padStart(2, '0');
            const seg = (segundos % 60).toString().padStart(2, '0');
            document.getElementById('timer-display').innerText = `${min}:${seg}`;
        }

        function toggleTimerButtons(estado) {
            if (estado === 'inicio') {
                document.getElementById('btn-inicio').classList.add('d-none');
                document.getElementById('btn-fin').classList.remove('d-none');
                document.getElementById('btn-stop').classList.add('d-none');
                document.getElementById('btn-cerrar').classList.add('d-none');
            } else if (estado === 'fin') {
                document.getElementById('btn-fin').classList.add('d-none');
                document.getElementById('btn-stop').classList.remove('d-none');
                document.getElementById('btn-cerrar').classList.add('d-none');
            }
        }

        function generarQR(juezId) {
            const modal = document.getElementById('modal-qr');
            const titulo = document.getElementById('qr-title');

            titulo.innerText = `JUEZ ${juezId}`;
            const urlAcceso = `${window.location.origin}/calificar/juez/${juezId}?t=${Date.now()}`;

            qrcodeObject.clear();
            qrcodeObject.makeCode(urlAcceso);
            modal.classList.remove('d-none');
        }

        function cerrarModal() {
            document.getElementById('modal-qr').classList.add('d-none');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarModal();
        });
    </script>

    <style>
        .kata-board {
            height: 100vh;
            overflow: hidden;
            background: #f3f4f6;
        }

        .kata-main {
            height: 80vh;
            display: grid;
            grid-template-columns: 1fr minmax(320px, 34vw) 1fr;
        }

        .competitor-panel {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-align: center;
            border-color: #ffffff;
            border-style: solid;
        }

        .competitor-blue {
            background: #0d47a1;
            border-width: 0 8px 0 0;
        }

        .competitor-red {
            background: #b71c1c;
            border-width: 0 0 0 8px;
        }

        .competitor-state {
            font-size: clamp(1.6rem, 3vw, 3rem);
            font-weight: 900;
            letter-spacing: 0.08em;
        }

        .competitor-name {
            font-size: clamp(2rem, 4vw, 4.5rem);
            font-weight: 800;
        }

        .timer-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2rem;
            padding: 1.5rem;
            background: #ffffff;
        }

        .timer-display-wrap {
            width: 100%;
            padding: 3rem 1rem;
            text-align: center;
            background: #ffc107;
            border: 4px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: inset 0 2px 12px rgba(15, 23, 42, 0.18);
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
            gap: 1rem;
        }

        .judges-panel {
            height: 20vh;
            display: flex;
            align-items: center;
            justify-content: space-around;
            gap: 1rem;
            padding: 1rem 2rem;
            background: #ffffff;
            border-top: 8px solid #d1d5db;
        }

        .score-input {
            width: 5rem;
            padding: 0.5rem;
            border: 2px solid transparent;
            border-radius: 0.5rem;
            text-align: center;
            color: #ffffff;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .score-blue {
            background: #0d47a1;
            border-color: #07356f;
        }

        .score-red {
            background: #b71c1c;
            border-color: #7f1010;
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
            flex-direction: column;
            align-items: center;
            padding: 2.5rem;
            background: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
        }

        .qr-box {
            padding: 1rem;
            background: #ffffff;
            border: 4px solid #f3f4f6;
            border-radius: 0.5rem;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        @media (max-width: 992px) {
            .kata-main {
                grid-template-columns: 1fr;
                height: auto;
                min-height: 80vh;
            }

            .competitor-panel {
                min-height: 180px;
                border-width: 0 0 6px;
            }

            .judges-panel {
                height: auto;
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html>
