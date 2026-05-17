<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kumite Temporizador</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --aka: #d60000;
            --ao: #00549f;
            --panel-bg: #f8fafc;
            --page-bg: #0f172a;
            --yellow: #ffff00;
        }

        * {
            box-sizing: border-box;
        }

        .bg-red {
            background-color: #ecf1bd; /* Rojo intenso */
            color: #000000;              /* Texto en negro */
            padding: 15px;
            border-radius: 10px;
            text-align: center;        /* Opcional: centrar el contenido */
        }

        .bg-red h3, .bg-red h4 {
            margin: 5px 0;             /* Ajusta el espacio entre líneas */
        }

        .bg-red h4 {
            font-size: clamp(1rem, 2vw, 1.35rem);
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .toast-senshu-arriba {
            width: 340px !important;
            margin-top: 18px !important;
            background: #ffff00 !important;
            color: #000000 !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.28) !important;
        }

        html,
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            overflow: hidden;
            background: var(--page-bg);
            font-family: Arial, Helvetica, sans-serif;
        }

        button {
            min-height: 40px;
            border: 1px solid #2f2f30;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            text-transform: uppercase;
            transition: transform 0.1s ease, box-shadow 0.15s ease, filter 0.15s ease;
        }

        button:disabled {
            cursor: not-allowed;
            opacity: 0.55;
        }

        button:not(:disabled):hover {
            filter: brightness(1.05);
        }

        button:not(:disabled):active {
            transform: translateY(1px);
        }

        .hidden {
            display: none !important;
        }

        .kumite-tablero {
            display: grid;
            grid-template-columns: minmax(280px, 1fr) minmax(330px, 450px) minmax(280px, 1fr);
            gap: 12px;
            width: 100%;
            height: 100vh;
            max-height: 100vh;
            padding: 8px;
            overflow: hidden;
        }

        .marcador,
        .timer-panel {
            min-height: 0;
            height: 100%;
            overflow: hidden;
        }

        .marcador {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 6px;
            padding: 10px;
            border: 4px solid #ffffff;
            border-radius: 14px;
            color: #ffffff;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.4);
        }

        .marcador-aka {
            background: var(--aka);
        }

        .marcador-ao {
            background: var(--ao);
        }

        .pantalla-puntos {
            position: relative;
            z-index: 0;
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            pointer-events: none;
        }

        .estado-combate {
            display: block;
            width: 100%;
            font-weight: 900;
            line-height: 1;
        }

        .nombre-competidor {
            display: block;
            width: 100%;
            min-height: 24px;
            color: #fbfcfd;
            font-weight: 900;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .puntos-gigantes {
            color: inherit;
            font-family: "Arial Black", Arial, Helvetica, sans-serif;
            font-size: clamp(15rem, 38vh, 22rem);
            font-weight: 900;
            line-height: 0.82;
            text-align: center;
            text-shadow: 4px 4px 0 rgba(0, 0, 0, 0.25);
            transition: transform 0.1s ease;
        }

        .marcador-aka .puntos-gigantes {
            color: #ffffff;
        }

        .marcador-ao .puntos-gigantes {
            color: #ffffff;
        }

        .panel-control {
            position: relative;
            z-index: 2;
            display: flex;
            flex: 0 0 auto;
            flex-direction: column;
            gap: 6px;
            width: 100%;
        }

        .fila {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px;
            align-items: center;
            width: 100%;
        }

        .fila-faltas {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .fila-kiken {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }

        .btn-kiken {
            grid-column: 5;
            background: linear-gradient(145deg, #fef3c7, #f59e0b) !important;
            color: #000000 !important;
            min-height: 30px !important;
            font-weight: 800;
        }

        .contadores-tecnicas {
            color: #ffffff; /* Texto blanco para contraste con el fondo rojo */
            font-family: 'Oswald', sans-serif; /* Aplicar fuente Oswald */
            font-size: 10px;
            font-weight: 700;
            font-variant-numeric: tabular-nums; /* Para que los números no se desplacen */
            text-align: center;
        }

        .btn-personalizado,
        .btn-personalizadoAzul,
        .btn-senshu,
        .btn-hantei,
        .btn-falta,
        .btn-kiken,
        .btn-Reloj {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 6px 8px;
            background: linear-gradient(145deg, #f7f7f7, #d1d5db);
            color: #000000 !important;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.28), -1px -1px 4px rgba(255, 255, 255, 0.45);
            line-height: 1;
        }

        .btn-Reloj {
            min-height: 32px;
            padding: 3px 6px;
            font-size: 1.25rem;
        }

        .marcador .btn-personalizadoAzul,
        .marcador .btn-senshu,
        .marcador .btn-hantei,
        .marcador .btn-falta,
        .marcador .btn-kiken {
            min-height: 32px;
            padding: 4px 6px;
            border-radius: 7px;
            font-size: 0.78rem;
        }

        .btn-cerrar-tablero {
            background: #ffff00 !important;
            color: #000000 !important;
            border-color: #000000 !important;
        }

        .btn-nuevo-combate {
            background: linear-gradient(145deg, #f7f7f7, #d1d5db) !important;
            color: #000000 !important;
        }

        .btn-start {
            background: #16a34a;
            color: #ffffff !important;
        }

        .btn-pause {
            background: #eab308;
            color: #ffffff !important;
        }

        .btn-reset {
            background: #dc2626;
            color: #ffffff !important;
        }

        .falta-activa,
        .senshu-activo,
        .hantei-activo {
            background: var(--yellow) !important;
            color: #000000 !important;
            border-color: #000000 !important;
        }

        .proximo-combate {
            position: relative;
            z-index: 2;
            flex: 0 0 auto;
            width: 100%;
        }

        .proximo-combate label {
            margin-bottom: 3px;
            color: #e5e7eb;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .proximo-combate input {
            width: 100%;
            height: 36px;
            color: #000000 !important;
            font-weight: 700;
            text-transform: uppercase;
        }

        .timer-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 14px;
            padding: 12px;
            border-radius: 14px;
            background: #ffffff;
            text-align: center;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.3);
        }

        .timer-panel-brand {
            font-size: 13px;
            font-family: 'Calibri', sans-serif;
            font-weight: bold;
            margin-top: -10px;
        }

        .timer-display {
            width: 100%;
            padding: 34px 8px;
            border: 8px solid #e5e7eb;
            border-radius: 10px;
            background: #f3f4f6;
            color: #dc2626;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: clamp(4rem, 10vw, 8.5rem);
            font-weight: 900;
            line-height: 1;
            box-shadow: inset 0 4px 16px rgba(15, 23, 42, 0.14);
        }

        .fase-combate {
            align-self: center;
            background: #ffff00;
            border: 2px solid #000000;
            border-radius: 999px;
            color: #000000;
            display: inline-flex;
            font-size: 1rem;
            font-weight: 900;
            justify-content: center;
            line-height: 1;
            margin: -22px 0 -22px;
            min-width: 190px;
            padding: 9px 18px;
            position: relative;
            text-transform: uppercase;
            z-index: 3;
        }

        .timer-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .ajuste-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(94px, 1fr) minmax(100px, 1.05fr);
            gap: 10px;
            align-items: stretch;
        }

        .ajuste-col {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .ajuste-label {
            min-height: 18px;
            color: #1f2937;
            font-size: 0.9rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .tiempo-select {
            width: 100%;
            border: 1px solid #2f2f30;
            border-radius: 8px;
            color: #000000;
            font-size: 0.92rem;
            font-weight: 800;
            text-align: center;
            text-align-last: center;
        }

        .ajuste-col .btn-personalizado,
        .ajuste-col .btn-Reloj,
        .ajuste-col .tiempo-select {
            min-height: 38px;
            height: 38px;
            box-sizing: border-box;
        }

        .ajuste-col .btn-personalizado {
            padding: 4px 6px;
            font-size: 0.95rem;
            white-space: nowrap;
        }

        .ajuste-col .btn-Reloj {
            padding: 4px 6px;
            font-size: 1.2rem;
        }

        .senshu-indicador {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border: 2px solid #000000;
            border-radius: 50%;
            background: var(--yellow);
            color: #000000;
            font-family: "Arial Black", Arial, Helvetica, sans-serif;
            font-size: 2.4rem;
            font-weight: 900;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.35);
        }

        .overlay-ganador {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.9);
            text-align: center;
        }

        .mensaje-contenedor {
            width: min(70vw, 900px);
            padding: 48px;
            border: 10px solid #ff0000;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 0 50px rgba(255, 0, 0, 0.45);
        }

        .texto-arriba {
            margin: 0;
            color: #ff0000;
            font-family: "Arial Black", Arial, Helvetica, sans-serif;
            font-size: clamp(2.6rem, 6vw, 5rem);
            font-weight: 900;
        }

        .texto-debajo {
            margin-top: 18px;
            color: #000000;
            font-size: clamp(1.5rem, 3vw, 2.3rem);
            font-weight: 800;
            text-transform: uppercase;
        }

        .texto-organizacion {
            margin-top: 8px;
            color: #000000;
            font-size: clamp(1.1rem, 2.2vw, 1.8rem);
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-cerrar-anuncio {
            width: auto;
            margin-top: 34px;
            padding: 14px 38px;
            background: #333333;
            color: #ffffff !important;
            font-size: 1.1rem;
        }

        @media (max-width: 1100px) {
            html,
            body {
                overflow: auto;
            }

            .kumite-tablero {
                grid-template-columns: 1fr;
                height: auto;
                min-height: 100vh;
                max-height: none;
                overflow: visible;
            }

            .marcador,
            .timer-panel {
                min-height: 100vh;
            }
        }
    </style>
</head>

<body>
    <main class="kumite-tablero">
        <section class="marcador marcador-aka">
            <div id="contenedor-s-rojo"></div>

            <div class="pantalla-puntos">
                <span class="estado-combate">EN COMBATE</span>
                <span id="mirrorSpanRojo" class="nombre-competidor">---</span>
                <div id="puntosRojo" class="puntos-gigantes">0</div>
            </div>

            <div class="panel-control">
                <div class="fila">
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('aka', 1)">+ Yuko</button>
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('aka', 2)">+ Wazari</button>
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('aka', 3)">+ Ippon</button>
                    <button id="btn-senshu-rojo" class="btn-senshu" onclick="toggleSenshu('aka')">Senshu</button>
                </div>
                <div class="fila contadores-tecnicas">
                    <span id="mirrorSpanYukoRojo">0</span>
                    <span id="mirrorSpanWazariRojo">0</span>
                    <span id="mirrorSpanIpponRojo">0</span>
                </div>
                <div class="fila">
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('aka', -1)">- Yuko</button>
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('aka', -2)">- Wazari</button>
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('aka', -3)">- Ippon</button>
                    <button id="btn-hantei-rojo" class="btn-hantei" onclick="logicaHantei('aka')">Hantei</button>
                </div>
                <div class="fila fila-faltas">
                    <button id="btn-aka-c1" class="btn-falta" onclick="togglePenalty('aka', 1)">C1</button>
                    <button id="btn-aka-c2" class="btn-falta" onclick="togglePenalty('aka', 2)">C2</button>
                    <button id="btn-aka-c3" class="btn-falta" onclick="togglePenalty('aka', 3)">C3</button>
                    <button id="btn-aka-hc" class="btn-falta" onclick="togglePenalty('aka', 4)">HC</button>
                    <button id="btn-aka-c" class="btn-falta" onclick="togglePenalty('aka', 5)">C</button>
                </div>
                <div class="fila fila-kiken">
                    <button id="btn-kiken-rojo" class="btn-kiken" onclick="declararKiken('aka')">Kiken</button>
                </div>
            </div>

            <div class="proximo-combate">
                <label for="TxtRojoProximo">Proximo Combate</label>
                <input
                    type="text"
                    id="TxtRojoProximo"
                    name="TxtRojoProximo"
                    class="form-control form-control-lg"
                    placeholder="Ingrese el proximo combate..."
                >
                @error('TxtRojoProximo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </section>

        <section class="timer-panel">
            <div class="bg-red">
                <h3 id="tablero-modalidad-label">Modalidad: Kumite Individual</h3>
                <h4>Categoría: </h4> 
            </div>
            <div id="fase-combate-label" class="fase-combate">Combate</div>
            <div id="timer-display" class="timer-display">00:00</div>

            <div class="timer-grid">
                <button id="btn-start" onclick="startTimer()" class="btn-start">Inicio</button>
                <button id="btn-pause" onclick="pauseTimer()" class="btn-pause hidden">Pausa</button>
                <button id="btn-reset" onclick="resetTimer()" class="btn-reset">Reset</button>
            </div>

            <div class="ajuste-grid">
                <div class="ajuste-col">
                    <span class="ajuste-label">Minutos</span>
                    <button onclick="adjustTime(60)" class="btn-Reloj">+</button>
                    <button onclick="adjustTime(-60)" class="btn-Reloj">-</button>
                </div>
                <div class="ajuste-col">
                    <span class="ajuste-label">Segundos</span>
                    <button onclick="adjustTime(1)" class="btn-Reloj">+</button>
                    <button onclick="adjustTime(-1)" class="btn-Reloj">-</button>
                </div>
                <div class="ajuste-col">
                    <label for="tiempo" class="ajuste-label">Tiempo</label>
                    <select id="tiempo" name="tiempo" class="tiempo-select" onchange="setPresetTime(this.value)">
                        <option value="">--</option>
                        <option value="60">1:00</option>
                        <option value="90">1:30</option>
                        <option value="120">2:00</option>
                        <option value="180">3:00</option>
                    </select>
                    <button type="button" id="btnAnteriorCombate" onclick="volverCombateAnterior()" class="btn-personalizado btn-nuevo-combate">
                        Anterior
                    </button>
                </div>
                <div class="ajuste-col">
                    <span class="ajuste-label">&nbsp;</span>
                    <button id="btnMuestraGanador" class="btn-personalizado" onclick="declararGanador()" disabled>Ganador</button>
                    <button type="button" id="btnNuevoCombate" onclick="trasladarDatos()" class="btn-personalizado btn-nuevo-combate">
                        Nuevo
                    </button>
                </div>
            </div>
            <button id="btnCerrar" onclick="window.location.href='{{ route('dashboard') }}'" class="btn-personalizado btn-cerrar-tablero">Cerrar</button>
        </section>

        <section class="marcador marcador-ao">
            <div id="contenedor-s-azul"></div>

            <div class="pantalla-puntos">
                <span class="estado-combate">EN COMBATE</span>
                <span id="mirrorSpanAzul" class="nombre-competidor">---</span>
                <div id="puntosAzul" class="puntos-gigantes">0</div>
            </div>

            <div class="panel-control">
                <div class="fila">
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('ao', 1)">+ Yuko</button>
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('ao', 2)">+ Wazari</button>
                    <button class="btn-personalizadoAzul suma" onclick="updateScore('ao', 3)">+ Ippon</button>
                    <button id="btn-senshu-azul" class="btn-senshu" onclick="toggleSenshu('ao')">Senshu</button>
                </div>
                <div class="fila contadores-tecnicas">
                    <span id="mirrorSpanYukoAzul">0</span>
                    <span id="mirrorSpanWazariAzul">0</span>
                    <span id="mirrorSpanIpponAzul">0</span>
                    <span></span>
                </div>
                <div class="fila">
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('ao', -1)">- Yuko</button>
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('ao', -2)">- Wazari</button>
                    <button class="btn-personalizadoAzul resta" onclick="updateScore('ao', -3)">- Ippon</button>
                    <button id="btn-hantei-azul" class="btn-hantei" onclick="logicaHantei('ao')">Hantei</button>
                </div>
                <div class="fila fila-faltas">
                    <button id="btn-ao-c1" class="btn-falta" onclick="togglePenalty('ao', 1)">C1</button>
                    <button id="btn-ao-c2" class="btn-falta" onclick="togglePenalty('ao', 2)">C2</button>
                    <button id="btn-ao-c3" class="btn-falta" onclick="togglePenalty('ao', 3)">C3</button>
                    <button id="btn-ao-hc" class="btn-falta" onclick="togglePenalty('ao', 4)">HC</button>
                    <button id="btn-ao-c" class="btn-falta" onclick="togglePenalty('ao', 5)">C</button>
                </div>
                <div class="fila fila-kiken">
                    <button id="btn-kiken-azul" class="btn-kiken" onclick="declararKiken('ao')">Kiken</button>
                </div>
            </div>

            <div class="proximo-combate">
                <label for="TxtAzulProximo">Proximo Combate</label>
                <input
                    type="text"
                    id="TxtAzulProximo"
                    name="TxtAzulProximo"
                    class="form-control form-control-lg"
                    placeholder="Ingrese el proximo combate..."
                >
                @error('TxtAzulProximo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </section>
    </main>

    <div id="modal-senshu" class="overlay-ganador">
        <div class="mensaje-contenedor" style="border-color: #ffff00; box-shadow: 0 0 50px rgba(255, 255, 0, 0.5);">
            <p class="texto-arriba" style="color: #000000;">AVISO</p>
            <p class="texto-debajo">EL OTRO COMPETIDOR YA TIENE SENSHU</p>
            <button onclick="cerrarModalSenshu()" class="btn-cerrar-anuncio" style="background-color: #ffff00; color: #000000 !important; border: 2px solid #000000;">Cerrar</button>
        </div>
    </div>

    <div id="modal-ganador" class="overlay-ganador">
        <div id="contenedor-ganador" class="mensaje-contenedor">
            <h3 id="texto-ganador-titulo" class="texto-arriba"></h3>
            <h3 id="texto-ganador-nombre" class="texto-debajo"></h3>
            <div id="texto-ganador-organizacion" class="texto-organizacion"></div>
            <button onclick="cerrarModalGanador()" class="btn-cerrar-anuncio">Cerrar</button>
        </div>
    </div>

    <script>
        let timerInterval = null;
        let timerSeconds = 0;
        let scores = { ao: 0, aka: 0 };
        let activeSenshu = null;
        let penalties = {
            ao: [false, false, false, false, false],
            aka: [false, false, false, false, false]
        };
        let indiceCombateKumite = -1;
        let proximoIndiceCombateKumite = null;
        let proximoIndiceVisibleKumite = null;
        let historialCombatesKumite = [];
        const combateInicialKumite = @json($combateInicialKumite);
        const combatesKumite = @json($combatesKumite);
        const csrfToken = @json(csrf_token());
        const guardarCombateUrl = @json(route('tablero.kumite.combates.store'));
        const podioKumiteUrl = combateInicialKumite.sorteo_id
            ? @json(route('tablero.kumite.podio')) + `?sorteo_id=${combateInicialKumite.sorteo_id}`
            : null;
        const siguienteCategoriaKumiteUrl = combateInicialKumite.siguiente_sorteo_id
            ? @json(route('tablero.kumite')) + `?sorteo_id=${combateInicialKumite.siguiente_sorteo_id}`
            : null;

        const sideConfig = {
            ao: {
                colorName: 'azul',
                displayId: 'puntosAzul',
                nameId: 'mirrorSpanAzul',
                title: 'GANADOR COMPETIDOR AZUL',
                background: '#004a99'
            },
            aka: {
                colorName: 'rojo',
                displayId: 'puntosRojo',
                nameId: 'mirrorSpanRojo',
                title: 'GANADOR COMPETIDOR ROJO',
                background: '#cc0000'
            }
        };

        const penaltyNames = ['C1', 'C2', 'C3', 'HC', 'C'];

        document.addEventListener('DOMContentLoaded', function () {
            ['TxtAzulProximo', 'TxtRojoProximo'].forEach(function (id) {
                const input = document.getElementById(id);
                if (!input) return;

                input.addEventListener('input', function (event) {
                    event.target.value = event.target.value.toUpperCase();
                    event.target.classList.toggle('border-primary', event.target.value.length > 0);
                });
            });

            timerSeconds = tiempoSeleccionadoSegundos();
            updateTimerDisplay();
            inicializarEncabezadoCombate();
            setEncabezadoCombate();
            prepararPrimerCombateEnCampos();
        });

        function inicializarEncabezadoCombate() {
            $('#tablero-modalidad-label').text('Modalidad: Kumite Individual');
            $('.timer-panel .bg-red h4').text('Categoría:');
        }

        function setEncabezadoCombate(modalidad = null, categoria = null) {
            const modalidadTexto = modalidad || combateInicialKumite.modalidad || 'Kumite Individual';
            const categoriaTexto = formatearCategoriaVisual(categoria ?? combateInicialKumite.categoria ?? '');

            $('#tablero-modalidad-label').text(`Modalidad: ${modalidadTexto}`);
            $('.timer-panel .bg-red h4').text(categoriaTexto ? `Categoría: ${categoriaTexto}` : 'Categoría:');
        }

        function formatearCategoriaVisual(categoria) {
            return (categoria || '')
                .replace(/menor o igual a/gi, '<=')
                .replace(/mayor o igual a/gi, '>=');
        }

        function cargarPrimerCombateDesdeLlave() {
            const primerIndice = Number.isInteger(combateInicialKumite.indice_combate)
                ? combateInicialKumite.indice_combate
                : encontrarSiguienteCombateValido(0);

            if (primerIndice !== null) {
                return cargarCombateDesdeLlave(primerIndice);
            }

            const primerIndiceVisible = encontrarSiguienteCombateVisible(0);

            return primerIndiceVisible !== null ? cargarCombateVisibleDesdeLlave(primerIndiceVisible) : false;
        }

        function prepararPrimerCombateEnCampos() {
            const primerIndice = Number.isInteger(combateInicialKumite.indice_combate)
                ? combateInicialKumite.indice_combate
                : encontrarSiguienteCombateValido(0);

            proximoIndiceCombateKumite = primerIndice;
            proximoIndiceVisibleKumite = primerIndice ?? encontrarSiguienteCombateVisible(0);
            mostrarCombateProximoEnCampos(proximoIndiceVisibleKumite);
        }

        function cargarCombateDesdeLlave(index) {
            const combate = combatesKumite[index] || null;
            const nombreRojo = (combate?.rojo || '').trim();
            const nombreAzul = (combate?.azul || '').trim();

            if (!nombreRojo && !nombreAzul) {
                return false;
            }

            $('#mirrorSpanRojo').text(nombreRojo || '---');
            $('#mirrorSpanAzul').text(nombreAzul || '---');
            $('#mirrorSpanRojo').data('organizacion', combate?.rojo_organizacion || '');
            $('#mirrorSpanAzul').data('organizacion', combate?.azul_organizacion || '');
            setEncabezadoCombate(combateInicialKumite.modalidad, combateInicialKumite.categoria);
            setFaseCombate(combate.ronda || 'Combate');
            indiceCombateKumite = index;
            cargarSiguienteCombateEnCampos();

            return true;
        }

        function cargarCombateVisibleDesdeLlave(index) {
            const combate = combatesKumite[index] || null;

            if (!combate) {
                return false;
            }

            $('#mirrorSpanRojo').text(nombreVisibleProximo(combate.rojo || '') || '---');
            $('#mirrorSpanAzul').text(nombreVisibleProximo(combate.azul || '') || '---');
            $('#mirrorSpanRojo').data('organizacion', nombreVisibleProximo(combate.rojo || '') ? (combate.rojo_organizacion || '') : '');
            $('#mirrorSpanAzul').data('organizacion', nombreVisibleProximo(combate.azul || '') ? (combate.azul_organizacion || '') : '');
            setEncabezadoCombate(combateInicialKumite.modalidad, combateInicialKumite.categoria);
            setFaseCombate(combate.ronda || 'Combate');
            indiceCombateKumite = index;
            cargarSiguienteCombateEnCampos();

            return true;
        }

        function guardarCombateActualEnHistorial() {
            if (indiceCombateKumite < 0) {
                return;
            }

            const actual = combatesKumite[indiceCombateKumite] || null;

            if (!actual) {
                return;
            }

            historialCombatesKumite.push({
                index: indiceCombateKumite,
                visible: esCombateBye(actual),
            });
        }

        function volverCombateAnterior() {
            if (historialCombatesKumite.length === 0) {
                showToast('NO HAY COMBATE ANTERIOR', 'info');
                return;
            }

            const anterior = historialCombatesKumite.pop();
            limpiarDatosCombate();

            if (anterior.visible) {
                cargarCombateVisibleDesdeLlave(anterior.index);
            } else {
                cargarCombateDesdeLlave(anterior.index);
            }
        }

        function setFaseCombate(fase) {
            $('#fase-combate-label').text(fase || 'Combate');
        }

        function cargarSiguienteCombateEnCampos() {
            proximoIndiceCombateKumite = encontrarSiguienteCombateValido(indiceCombateKumite + 1);
            proximoIndiceVisibleKumite = proximoIndiceCombateKumite !== null
                ? proximoIndiceCombateKumite
                : encontrarSiguienteCombateVisible(indiceCombateKumite + 1);

            mostrarCombateProximoEnCampos(proximoIndiceVisibleKumite);
        }

        function mostrarCombateProximoEnCampos(index) {
            const siguiente = index !== null ? combatesKumite[index] : null;
            const inputAzul = document.getElementById('TxtAzulProximo');
            const inputRojo = document.getElementById('TxtRojoProximo');

            inputRojo.value = nombreVisibleProximo(siguiente?.rojo || '');
            inputAzul.value = nombreVisibleProximo(siguiente?.azul || '');
            inputRojo.classList.toggle('border-primary', inputRojo.value.length > 0);
            inputAzul.classList.toggle('border-primary', inputAzul.value.length > 0);
        }

        function encontrarSiguienteCombateValido(desde) {
            for (let index = desde; index < combatesKumite.length; index++) {
                if (!combatesKumite[index].realizado && !esCombateBye(combatesKumite[index])) {
                    return index;
                }
            }

            return null;
        }

        function encontrarSiguienteCombateVisible(desde) {
            for (let index = desde; index < combatesKumite.length; index++) {
                const combate = combatesKumite[index];

                if (!combate.realizado && tieneCompetidorVisible(combate)) {
                    return index;
                }
            }

            return null;
        }

        function esCombateBye(combate) {
            const rojo = (combate?.rojo || '').trim().toUpperCase();
            const azul = (combate?.azul || '').trim().toUpperCase();

            return !rojo || !azul || rojo === 'BYE' || azul === 'BYE';
        }

        function tieneCompetidorVisible(combate) {
            return nombreVisibleProximo(combate?.rojo || '') !== ''
                || nombreVisibleProximo(combate?.azul || '') !== '';
        }

        function nombreVisibleProximo(nombre) {
            const texto = (nombre || '').trim();
            const textoMayuscula = texto.toUpperCase();

            if (!texto || textoMayuscula === 'BYE' || textoMayuscula.startsWith('GANADOR')) {
                return '';
            }

            return texto;
        }

        function showToast(title, icon = 'warning') {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#FFFF00'
            });
        }

        function showCenteredToast(title, icon = 'warning') {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#FFFF00',
                color: '#000000',
                customClass: {
                    popup: 'toast-senshu-arriba'
                }
            });
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timerSeconds / 60).toString().padStart(2, '0');
            const seconds = (timerSeconds % 60).toString().padStart(2, '0');
            $('#timer-display').text(`${minutes}:${seconds}`);
            controlarEstadoBoton();
        }

        function startTimer() {
            if (timerSeconds <= 0) {
                showToast('EL TIEMPO DEBE SER MAYOR A CERO');
                return;
            }

            if (timerInterval) return;

            timerInterval = setInterval(function () {
                if (timerSeconds > 0) {
                    timerSeconds--;
                    updateTimerDisplay();
                    return;
                }

                pauseTimer();
                showToast('TIEMPO FINALIZADO', 'info');
            }, 1000);

            $('#btn-start').addClass('hidden');
            $('#btn-pause').removeClass('hidden');
            toggleTimerControls();
        }

        function pauseTimer() {
            clearInterval(timerInterval);
            timerInterval = null;
            $('#btn-start').removeClass('hidden');
            $('#btn-pause').addClass('hidden');
            toggleTimerControls();
        }

        function resetTimer() {
            pauseTimer();
            timerSeconds = tiempoSeleccionadoSegundos();
            updateTimerDisplay();
        }

        function toggleTimerControls() {
            controlarEstadoBoton();
        }

        function adjustTime(value) {
            if (timerInterval) return;
            timerSeconds = Math.max(0, timerSeconds + value);
            updateTimerDisplay();
        }

        function setPresetTime(value) {
            if (timerInterval || value === '') return;
            timerSeconds = parseInt(value, 10) || 0;
            updateTimerDisplay();
        }

        function tiempoSeleccionadoSegundos() {
            const tiempoSelect = document.getElementById('tiempo');

            if (!tiempoSelect || tiempoSelect.value === '') {
                return 0;
            }

            return parseInt(tiempoSelect.value, 10) || 0;
        }

        function updateScore(side, value) {
            const previousScore = scores[side];
            scores[side] = Math.max(0, scores[side] + value);

            if (scores[side] === previousScore && value < 0) return;

            const displayId = sideConfig[side].displayId;
            $(`#${displayId}`).text(scores[side]).css('transform', 'scale(1.12)');
            setTimeout(function () {
                $(`#${displayId}`).css('transform', 'scale(1)');
            }, 100);

            updateTechniqueCounter(side, value);
        }

        function updateTechniqueCounter(side, value) {
            const absValue = Math.abs(value);
            const technique = absValue === 1 ? 'Yuko' : (absValue === 2 ? 'Wazari' : 'Ippon');
            const sideName = side === 'ao' ? 'Azul' : 'Rojo';
            const mirrorId = `#mirrorSpan${technique}${sideName}`;
            const currentValue = parseInt($(mirrorId).text(), 10) || 0;
            $(mirrorId).text(Math.max(0, currentValue + (value > 0 ? 1 : -1)));
        }

        function toggleSenshu(side) {
            const config = sideConfig[side];

            if (activeSenshu === side) {
                activeSenshu = null;
                $(`#btn-senshu-${config.colorName}`).removeClass('senshu-activo');
                $(`#contenedor-s-${config.colorName}`).empty();
                return;
            }

            if (activeSenshu && activeSenshu !== side) {
                showCenteredToast('EL OTRO COMPETIDOR YA TIENE SENSHU');
                return;
            }

            if (scores[side] === 0) {
                showToast('NO SE PUEDE DAR SENSHU SIN PUNTOS');
                return;
            }

            activeSenshu = side;
            $(`#btn-senshu-${config.colorName}`).addClass('senshu-activo');
            $(`#contenedor-s-${config.colorName}`).html('<div class="senshu-indicador">S</div>');
        }

        function cerrarModalSenshu() {
            $('#modal-senshu').css('display', 'none');
        }

        function togglePenalty(side, level) {
            const index = level - 1;
            const sidePenalties = penalties[side];

            if (sidePenalties[index]) {
                if (index < 4 && sidePenalties[index + 1]) {
                    showToast(`PRIMERO DEBE QUITAR ${penaltyNames[index + 1]}`);
                    return;
                }

                sidePenalties[index] = false;
            } else {
                if (level === 4) {
                    for (let i = 0; i < 4; i++) {
                        sidePenalties[i] = true;
                    }
                } else if (index > 0 && !sidePenalties[index - 1]) {
                    showToast('DEBE MARCAR LA PENALIZACION ANTERIOR PRIMERO');
                    return;
                } else {
                    sidePenalties[index] = true;
                }
            }

            renderPenalties(side);
        }

        function renderPenalties(side) {
            penalties[side].forEach(function (active, index) {
                const suffix = ['c1', 'c2', 'c3', 'hc', 'c'][index];
                $(`#btn-${side}-${suffix}`).toggleClass('falta-activa', active);
            });
        }

        async function declararGanador() {
            const winner = resolveWinner();

            if (winner === 'hantei') {
                mostrarModalGanador('DECISION DE HANTEI', 'DE LOS JUECES', '', '#ffffcc', '#000000');
                return;
            }

            await registrarGanador(winner);
        }

        async function declararKiken(loserSide) {
            const winnerSide = loserSide === 'aka' ? 'ao' : 'aka';

            scores[loserSide] = 0;
            scores[winnerSide] = 1;
            $('#puntosRojo').text(scores.aka);
            $('#puntosAzul').text(scores.ao);

            await registrarGanador(winnerSide, {
                kikenSide: loserSide,
            });
        }

        function resolveWinner() {
            if (scores.ao !== scores.aka) {
                return scores.ao > scores.aka ? 'ao' : 'aka';
            }

            if (activeSenshu) return activeSenshu;

            for (const technique of ['Ippon', 'Wazari', 'Yuko']) {
                const aoValue = parseInt($(`#mirrorSpan${technique}Azul`).text(), 10) || 0;
                const akaValue = parseInt($(`#mirrorSpan${technique}Rojo`).text(), 10) || 0;

                if (aoValue !== akaValue) {
                    return aoValue > akaValue ? 'ao' : 'aka';
                }
            }

            return 'hantei';
        }

        function mostrarModalGanador(titulo, nombre, organizacion, fondo, texto) {
            $('#texto-ganador-titulo').text(titulo).css('color', texto);
            $('#texto-ganador-nombre').text(nombre).css('color', texto);
            $('#texto-ganador-organizacion').text(organizacion || '').css('color', texto);
            $('#contenedor-ganador').css({
                'background-color': fondo,
                'border-color': texto === '#ffffff' ? '#ffffff' : '#000000'
            });
            $('#modal-ganador').css('display', 'flex');
            pauseTimer();
        }

        function cerrarModalGanador() {
            $('#modal-ganador').css('display', 'none');
        }

        async function registrarGanador(side, options = {}) {
            const config = sideConfig[side];
            const nombreGanador = $(`#${config.nameId}`).text();
            const organizacionGanador = $(`#${config.nameId}`).data('organizacion') || '';

            if (combateInicialKumite.sorteo_id && indiceCombateKumite >= 0) {
                const guardado = await guardarResultadoCombate(side, nombreGanador, options);

                if (!guardado) {
                    return;
                }

                if (combatesKumite[indiceCombateKumite]) {
                    combatesKumite[indiceCombateKumite].realizado = true;
                    combatesKumite[indiceCombateKumite].ganador = nombreGanador;
                    propagarGanadorLocal(side, nombreGanador, organizacionGanador);
                }
            }

            mostrarModalGanador(config.title, nombreGanador, organizacionGanador, config.background, '#ffffff');
            limpiarDatosCombate();
            avanzarSiguienteCombate();
        }

        function propagarGanadorLocal(side, nombreGanador, organizacionGanador) {
            const combateActual = combatesKumite[indiceCombateKumite] || null;

            if (!combateActual) {
                return;
            }

            const siguiente = combatesKumite.find(function (combate) {
                return combate.round_index === combateActual.round_index + 1
                    && combate.match_index === Math.floor(combateActual.match_index / 2);
            });

            if (!siguiente) {
                return;
            }

            const ladoDestino = combateActual.match_index % 2 === 0 ? 'rojo' : 'azul';
            siguiente[ladoDestino] = nombreGanador;
            siguiente[`${ladoDestino}_organizacion`] = organizacionGanador || '';
            siguiente.bye = false;
        }

        async function guardarResultadoCombate(side, nombreGanador, options = {}) {
            const combateActual = combatesKumite[indiceCombateKumite] || {};
            const kikenSide = options.kikenSide || null;

            try {
                const response = await fetch(guardarCombateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        sorteo_id: combateInicialKumite.sorteo_id,
                        numero_llave: combateActual.numero_llave || (indiceCombateKumite + 1),
                        indice_combate: indiceCombateKumite,
                        round_index: combateActual.round_index || 0,
                        match_index: combateActual.match_index || 0,
                        competidor_rojo: $('#mirrorSpanRojo').text().trim(),
                        competidor_azul: $('#mirrorSpanAzul').text().trim(),
                        puntaje_rojo: scores.aka,
                        puntaje_azul: scores.ao,
                        faltas_rojo: faltasActivas('aka'),
                        faltas_azul: faltasActivas('ao'),
                        senshu: activeSenshu === 'aka' ? 'rojo' : (activeSenshu === 'ao' ? 'azul' : null),
                        senshu_rojo: activeSenshu === 'aka',
                        senshu_azul: activeSenshu === 'ao',
                        kiken_rojo: kikenSide === 'aka',
                        kiken_azul: kikenSide === 'ao',
                        tecnicas_rojo: tecnicasCompetidor('Rojo'),
                        tecnicas_azul: tecnicasCompetidor('Azul'),
                        ganador: nombreGanador,
                        ganador_color: side === 'aka' ? 'rojo' : 'azul',
                    }),
                });

                if (!response.ok) {
                    throw new Error('No se pudo guardar el resultado del combate.');
                }

                return true;
            } catch (error) {
                showToast(error.message || 'NO SE PUDO GUARDAR EL RESULTADO.', 'error');

                return false;
            }
        }

        function faltasActivas(side) {
            return penalties[side]
                .map(function (active, index) {
                    return active ? penaltyNames[index] : null;
                })
                .filter(Boolean);
        }

        function tecnicasCompetidor(sideName) {
            return {
                yuko: parseInt($(`#mirrorSpanYuko${sideName}`).text(), 10) || 0,
                wazari: parseInt($(`#mirrorSpanWazari${sideName}`).text(), 10) || 0,
                ippon: parseInt($(`#mirrorSpanIppon${sideName}`).text(), 10) || 0,
            };
        }

        function limpiarDatosCombate() {
            scores = { ao: 0, aka: 0 };
            penalties = {
                ao: [false, false, false, false, false],
                aka: [false, false, false, false, false]
            };
            activeSenshu = null;
            timerSeconds = tiempoSeleccionadoSegundos();

            $('#puntosAzul, #puntosRojo').text('0');
            $('#mirrorSpanYukoAzul, #mirrorSpanWazariAzul, #mirrorSpanIpponAzul, #mirrorSpanYukoRojo, #mirrorSpanWazariRojo, #mirrorSpanIpponRojo').text('0');
            $('#btn-senshu-azul, #btn-senshu-rojo').removeClass('senshu-activo');
            $('#btn-hantei-azul, #btn-hantei-rojo').removeClass('hantei-activo');
            $('#contenedor-s-azul, #contenedor-s-rojo').empty();
            renderPenalties('ao');
            renderPenalties('aka');
            updateTimerDisplay();
        }

        function avanzarSiguienteCombate() {
            const siguienteIndice = encontrarSiguienteCombateValido(indiceCombateKumite + 1);

            if (siguienteIndice === null) {
                $('#mirrorSpanRojo, #mirrorSpanAzul').text('---');
                $('#mirrorSpanRojo, #mirrorSpanAzul').data('organizacion', '');
                setFaseCombate('Categoria finalizada');
                proximoIndiceCombateKumite = null;
                cargarSiguienteCombateEnCampos();

                if (podioKumiteUrl) {
                    setTimeout(function () {
                        window.location.href = podioKumiteUrl;
                    }, 1800);
                }

                return;
            }

            cargarCombateDesdeLlave(siguienteIndice);
        }

        async function logicaHantei(side) {
            const config = sideConfig[side];
            const otherSide = side === 'ao' ? 'aka' : 'ao';
            const currentButton = $(`#btn-hantei-${config.colorName}`);
            const otherButton = $(`#btn-hantei-${sideConfig[otherSide].colorName}`);

            if (otherButton.hasClass('hantei-activo')) {
                showToast('SOLO PUEDE HABER UN GANADOR');
                return;
            }

            if (currentButton.hasClass('hantei-activo')) {
                currentButton.removeClass('hantei-activo');
                return;
            }

            currentButton.addClass('hantei-activo');
            await registrarGanador(side);
        }

        function trasladarDatos() {
            const inputAzul = document.getElementById('TxtAzulProximo');
            const inputRojo = document.getElementById('TxtRojoProximo');
            const nombreAzul = inputAzul.value.trim();
            const nombreRojo = inputRojo.value.trim();

            if (!nombreAzul && !nombreRojo) {
                const esPrimerCombate = $('#mirrorSpanAzul').text().trim() === '---'
                    && $('#mirrorSpanRojo').text().trim() === '---';

                if (esPrimerCombate) {
                    inicializarEncabezadoCombate();

                    if (cargarPrimerCombateDesdeLlave()) {
                        return;
                    }
                }

                return;
            }

            if (combatesKumite.length > 0 && proximoIndiceCombateKumite === null && proximoIndiceVisibleKumite === null) {
                guardarCombateActualEnHistorial();
                limpiarDatosCombate();
                $('#mirrorSpanAzul').text(nombreAzul || '---');
                $('#mirrorSpanRojo').text(nombreRojo || '---');
                $('#mirrorSpanAzul, #mirrorSpanRojo').data('organizacion', '');
                inputAzul.value = '';
                inputRojo.value = '';
                inputAzul.classList.remove('border-primary');
                inputRojo.classList.remove('border-primary');
                inputAzul.focus();

                return;
            }

            if (combatesKumite.length > 0 && proximoIndiceCombateKumite !== null) {
                guardarCombateActualEnHistorial();
                limpiarDatosCombate();
                cargarCombateDesdeLlave(proximoIndiceCombateKumite);
                inputAzul.focus();

                return;
            }

            if (combatesKumite.length > 0 && proximoIndiceVisibleKumite !== null) {
                guardarCombateActualEnHistorial();
                limpiarDatosCombate();
                cargarCombateVisibleDesdeLlave(proximoIndiceVisibleKumite);
                inputAzul.focus();

                return;
            }

            guardarCombateActualEnHistorial();
            $('#mirrorSpanAzul').text(nombreAzul || '---');
            $('#mirrorSpanRojo').text(nombreRojo || '---');
            $('#mirrorSpanAzul, #mirrorSpanRojo').data('organizacion', '');

            scores = { ao: 0, aka: 0 };
            penalties = {
                ao: [false, false, false, false, false],
                aka: [false, false, false, false, false]
            };
            activeSenshu = null;

            $('#puntosAzul, #puntosRojo').text('0');
            $('#mirrorSpanYukoAzul, #mirrorSpanWazariAzul, #mirrorSpanIpponAzul, #mirrorSpanYukoRojo, #mirrorSpanWazariRojo, #mirrorSpanIpponRojo').text('0');
            $('#btn-senshu-azul, #btn-senshu-rojo').removeClass('senshu-activo');
            $('#btn-hantei-azul, #btn-hantei-rojo').removeClass('hantei-activo');
            $('#contenedor-s-azul, #contenedor-s-rojo').empty();
            renderPenalties('ao');
            renderPenalties('aka');

            if (combatesKumite.length > 0) {
                indiceCombateKumite = proximoIndiceCombateKumite !== null
                    ? proximoIndiceCombateKumite
                    : indiceCombateKumite + 1;
                cargarSiguienteCombateEnCampos();
            } else {
                inputAzul.value = '';
                inputRojo.value = '';
                inputAzul.classList.remove('border-primary');
                inputRojo.classList.remove('border-primary');
            }

            inputAzul.focus();
        }

        function controlarEstadoBoton() {
            const btnNuevo = document.getElementById('btnNuevoCombate');
            const btnAnterior = document.getElementById('btnAnteriorCombate');
            const btnGanador = document.getElementById('btnMuestraGanador');
            const btnStart = document.getElementById('btn-start');
            const btnPause = document.getElementById('btn-pause');
            const btnReset = document.getElementById('btn-reset');
            const btnCerrar = document.getElementById('btnCerrar');
            const tiempoSelect = document.getElementById('tiempo');
            const ajusteButtons = document.querySelectorAll('.btn-Reloj');
            const combateButtons = document.querySelectorAll('.suma, .resta, .btn-senshu, .btn-hantei, .btn-falta, .btn-kiken');
            const isTimeZero = timerSeconds === 0;
            const isRunning = timerInterval !== null;
            const isPaused = !isRunning;

            if (isRunning) {
                if (btnStart) btnStart.disabled = true;
                if (btnPause) btnPause.disabled = false;
                if (btnReset) btnReset.disabled = true;
                if (btnCerrar) btnCerrar.disabled = true;
                if (btnNuevo) btnNuevo.disabled = true;
                if (btnAnterior) btnAnterior.disabled = true;
                if (btnGanador) btnGanador.disabled = true;
                if (tiempoSelect) tiempoSelect.disabled = true;
                ajusteButtons.forEach(function (button) {
                    button.disabled = true;
                });
                combateButtons.forEach(function (button) {
                    button.disabled = true;
                });
                return;
            }

            if (btnStart) btnStart.disabled = false;
            if (btnPause) btnPause.disabled = true;
            if (btnReset) btnReset.disabled = false;
            if (btnCerrar) btnCerrar.disabled = false;
            if (btnGanador) btnGanador.disabled = !(isTimeZero || isPaused);
            if (btnAnterior) {
                btnAnterior.disabled = !isTimeZero;
                btnAnterior.style.color = '#000000';
            }
            if (tiempoSelect) tiempoSelect.disabled = false;
            ajusteButtons.forEach(function (button) {
                button.disabled = false;
            });
            combateButtons.forEach(function (button) {
                button.disabled = isTimeZero;
            });

            if (btnNuevo) {
                btnNuevo.disabled = !isTimeZero;
                btnNuevo.style.color = '#000000';
            }
        }
    </script>
</body>
</html>
