<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero Kata</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --aka: #ff0000;
            --ao: #0070c0;
            --amarillo: #ffff00;
            --beige-claro: #f5e8c7;
            --naranja: #ff7f27;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            background: #f7f7f7;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            height: 100%;
            margin: 0;
            overflow: hidden;
            width: 100%;
        }

        button {
            border: 1px solid rgba(0, 0, 0, .28);
            border-radius: 7px;
            cursor: default;
            font-family: inherit;
            font-weight: 700;
            min-height: 44px;
        }

        .kata-screen {
            height: 100vh;
            min-height: 100vh;
            padding: 6px;
            width: 100vw;
        }

        .kata-board {
            background: #f7f7f7;
            border: 1px solid #cfd4da;
            display: grid;
            gap: 10px;
            grid-template-rows: auto minmax(0, 1fr) auto auto;
            height: calc(100vh - 12px);
            margin: 0;
            padding: 8px;
            width: calc(100vw - 12px);
        }

        .kata-header {
            align-items: center;
            background: var(--beige-claro);
            border: 1px solid #e2d2ad;
            border-radius: 9px;
            display: grid;
            font-size: clamp(1.15rem, 1.65vw, 1.85rem);
            font-weight: 700;
            gap: 28px;
            grid-template-columns: 1fr 1fr;
            line-height: 1.1;
            min-height: 54px;
            padding: 8px 16px;
        }

        .combat-layout {
            display: grid;
            gap: 10px;
            grid-template-columns: minmax(300px, 1fr) minmax(330px, 25vw) minmax(300px, 1fr);
            min-height: 0;
        }

        .competidor-col {
            display: grid;
            gap: 8px;
            grid-template-rows: minmax(0, 1fr) auto;
            min-height: 0;
        }

        .competidor {
            border-radius: 11px;
            color: #ffffff;
            display: grid;
            grid-template-rows: auto auto 1fr auto;
            min-height: 0;
            overflow: hidden;
        }

        .competidor-aka {
            background: var(--aka);
        }

        .competidor-ao {
            background: var(--ao);
        }

        .estado {
            border-bottom: 1px solid rgba(255, 255, 255, .55);
            font-size: clamp(1rem, 1.45vw, 1.75rem);
            font-weight: 700;
            line-height: 1.1;
            min-height: 58px;
            padding: 8px 12px;
            text-align: center;
        }

        .estado strong {
            display: block;
            font-size: clamp(1.05rem, 1.35vw, 1.65rem);
            text-transform: uppercase;
        }

        .kata-meta {
            border-bottom: 1px solid rgba(255, 255, 255, .55);
            font-size: clamp(1rem, 1.35vw, 1.6rem);
            font-weight: 700;
            line-height: 1.12;
            padding: 10px 12px;
        }

        .kata-meta span {
            display: block;
        }

        .kata-meta .kata-select-line {
            align-items: center;
            display: flex;
            gap: 5px;
            flex-wrap: nowrap;
        }

        .kata-select-wrap {
            flex: 0 0 auto;
            min-width: 0;
            position: relative;
        }

        .kata-label {
            flex: 0 0 auto;
            font-size: .82em;
            white-space: nowrap;
        }

        .kata-search {
            background: rgba(255, 255, 255, .98);
            border: 1px solid rgba(0, 0, 0, .28);
            border-radius: 6px 6px 0 0;
            color: #111827;
            display: none;
            font: inherit;
            font-size: .78em;
            font-weight: 700;
            height: 28px;
            min-width: 0;
            padding: 2px 6px;
            width: 220px;
        }

        .kata-select {
            background: rgba(255, 255, 255, .94);
            border: 1px solid rgba(0, 0, 0, .28);
            border-radius: 6px;
            color: #111827;
            font: inherit;
            font-size: .82em;
            font-weight: 700;
            height: 28px;
            min-width: 0;
            padding: 1px 6px;
            width: 220px;
        }

        .kata-select-wrap.is-searching .kata-search {
            display: block;
        }

        .kata-select-wrap.is-searching .kata-select {
            border-radius: 0 0 6px 6px;
        }

        .kata-ok {
            background: #ffd400;
            border: 1px solid rgba(0, 0, 0, .35);
            border-radius: 5px;
            color: #111827;
            cursor: pointer;
            flex: 0 0 auto;
            font-size: .72em;
            font-weight: 800;
            height: 30px;
            line-height: 1;
            padding: 3px 7px;
        }

        .kata-back {
            align-items: center;
            background: #ffd400;
            border: 1px solid rgba(0, 0, 0, .35);
            border-radius: 5px;
            color: #111827;
            cursor: pointer;
            display: none;
            flex: 0 0 auto;
            font-size: .78em;
            font-weight: 800;
            height: 30px;
            justify-content: center;
            line-height: 1;
            padding: 3px 8px;
        }

        .kata-numero-confirmado {
            display: none;
            font-weight: 800;
        }

        .kata-select-line.is-confirmed .kata-select,
        .kata-select-line.is-confirmed .kata-search,
        .kata-select-line.is-confirmed .kata-ok {
            display: none;
        }

        .kata-select-line.is-confirmed .kata-numero-confirmado {
            display: inline;
        }

        .kata-select-line.is-confirmed .kata-back {
            display: inline-flex;
        }

        .puntaje-panel {
            align-items: center;
            display: flex;
            font-size: clamp(5rem, 9vw, 11rem);
            font-weight: 700;
            justify-content: center;
            line-height: 1;
        }

        .puntaje-panel:empty {
            display: block;
        }

        .proximo {
            border-top: 1px solid rgba(255, 255, 255, .55);
            font-size: clamp(1rem, 1.35vw, 1.6rem);
            font-weight: 700;
            line-height: 1.05;
            min-height: 92px;
            padding: 10px 12px;
        }

        .proximo span {
            display: block;
            font-size: clamp(1.9rem, 3vw, 3.55rem);
            line-height: 1;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, .3);
        }

        .banderas-display {
            display: none;
            gap: 8px;
            min-height: 62px;
            padding-left: 12px;
        }

        .banderas-display.visible {
            display: flex;
        }

        .bandera-mini {
            display: inline-block;
            height: 48px;
            position: relative;
            width: 42px;
        }

        .bandera-mini::before {
            background: #d9a46a;
            content: "";
            height: 48px;
            left: 2px;
            position: absolute;
            top: 8px;
            width: 2px;
        }

        .bandera-mini::after {
            border-radius: 2px;
            box-shadow: 2px 2px 4px rgba(0, 0, 0, .18);
            content: "";
            height: 34px;
            left: 4px;
            position: absolute;
            top: 0;
            transform: skewY(3deg);
            width: 36px;
        }

        .bandera-roja::after {
            background: var(--aka);
        }

        .bandera-azul::after {
            background: #0000ff;
        }

        .center-panel {
            align-content: start;
            display: grid;
            gap: 14px;
            grid-template-columns: 1fr 1fr;
            min-width: 0;
            width: 100%;
        }

        .timer {
            align-items: center;
            background: var(--amarillo);
            border-radius: 10px;
            display: flex;
            font-size: clamp(4.8rem, 6.2vw, 6.5rem);
            font-weight: 900;
            grid-column: 1 / -1;
            justify-content: center;
            line-height: .9;
            min-height: 126px;
            min-width: 0;
            overflow: hidden;
            padding: 4px;
            white-space: nowrap;
            width: 100%;
        }

        .action-row {
            display: grid;
            gap: 10px;
            grid-column: 1 / -1;
            grid-template-columns: repeat(3, 1fr);
        }

        .action-row button {
            min-height: 56px;
        }

        .btn-ingreso {
            background: #6ab04a;
            color: #ffffff;
        }

        .btn-stop {
            background: #e46b24;
            color: #ffffff;
        }

        .btn-inicio {
            background: #4778c7;
            color: #ffffff;
        }

        .btn-tool {
            align-items: center;
            background: linear-gradient(#d9d9d9, #a6a6a6);
            box-shadow: 0 5px 9px rgba(0, 0, 0, .2);
            color: #000000;
            display: flex;
            font-size: clamp(1rem, 1.1vw, 1.25rem);
            gap: 9px;
            justify-content: center;
            min-height: 64px;
        }

        .btn-tool i {
            color: #173a61;
            font-size: 1.4rem;
        }

        .btn-tool.activo {
            background: var(--amarillo);
        }

        .resultado-select-wrap {
            display: none;
            min-width: 0;
        }

        .resultado-select-wrap.visible {
            display: block;
        }

        .resultado-select {
            background: #d9d9d9;
            border: 1px solid #000000;
            border-radius: 6px;
            color: #000000;
            font-family: inherit;
            font-size: clamp(.9rem, 1vw, 1.1rem);
            font-weight: 700;
            height: 64px;
            padding: 0 8px;
            width: 100%;
        }

        .judges {
            display: grid;
            gap: clamp(20px, 4.5vw, 70px);
            grid-template-columns: repeat(5, minmax(90px, 1fr));
            justify-items: center;
            margin: 0 auto;
            max-width: 980px;
            width: 100%;
        }

        .judges.hidden {
            display: none;
        }

        .judge {
            margin: 0 auto;
            width: clamp(92px, 8vw, 130px);
        }

        .judge-name {
            background: #d9d9d9;
            border: 1px solid #000000;
            border-bottom: 0;
            font-size: clamp(.85rem, 1.15vw, 1.25rem);
            font-weight: 500;
            line-height: 1;
            padding: 6px 4px;
            text-align: center;
        }

        .judge-flags {
            border: 1px solid #000000;
            display: grid;
            grid-template-columns: 1fr 1fr;
            height: clamp(35px, 5vh, 54px);
        }

        .judge-score-input {
            border: 0;
            color: #ffffff;
            font-family: inherit;
            font-size: clamp(1.3rem, 1.8vw, 2.1rem);
            font-weight: 700;
            min-width: 0;
            padding: 0 2px;
            text-align: center;
            width: 100%;
        }

        .judge-score-input:focus {
            outline: 2px solid #ffff00;
            outline-offset: -2px;
        }

        .judge-score-input.is-invalid {
            outline: 3px solid #ffff00;
            outline-offset: -3px;
        }

        .judge-score-input::placeholder {
            color: rgba(255, 255, 255, .75);
        }

        .flag-aka,
        .judge-score-red {
            background: var(--aka);
        }

        .flag-ao,
        .judge-score-blue {
            background: var(--ao);
        }

        .bottom-actions {
            display: grid;
            gap: clamp(28px, 5vw, 82px);
            grid-template-columns: repeat(4, minmax(100px, 1fr));
            margin: 0 auto;
            max-width: 760px;
            width: 100%;
        }

        .btn-bottom {
            background: linear-gradient(#d9d9d9, #a8a8a8);
            box-shadow: 0 5px 9px rgba(0, 0, 0, .2);
            color: #000000;
            min-height: 56px;
        }


        .btn-cerrar {
            background: var(--naranja);
            color: #000000;
            cursor: pointer;
        }

        .btn-kiken {
            box-shadow: 0 3px 6px rgba(0, 0, 0, .28);
            color: #ffffff;
            font-size: .85rem;
            line-height: 1;
            min-height: 30px;
            padding: 4px 10px;
            width: max-content;
        }

        .btn-kiken-aka {
            background: var(--aka);
            justify-self: start;
        }

        .btn-kiken-ao {
            background: var(--ao);
            justify-self: end;
        }

        .btn-kiken.is-active {
            background: var(--amarillo);
            color: #000000;
        }

        .kata-toast {
            background: #ffff00;
            border: 2px solid #000000;
            border-radius: 10px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .34);
            color: #000000;
            display: none;
            font-size: clamp(1.3rem, 1.8vw, 2rem);
            font-weight: 900;
            left: 50%;
            line-height: 1.15;
            max-width: min(92vw, 760px);
            min-width: min(92vw, 560px);
            padding: 18px 26px;
            position: fixed;
            text-align: center;
            top: 20px;
            transform: translateX(-50%);
            z-index: 9999;
        }

        .kata-toast.visible {
            display: block;
        }

        .kata-toast.is-error {
            background: #dc3545;
            border-color: #dc3545;
            color: #ffffff;
        }

        @media (max-width: 920px) {
            html,
            body {
                overflow: auto;
            }

            .kata-screen {
                height: auto;
                padding: 0;
            }

            .kata-board {
                height: auto;
                min-height: 100vh;
                width: 100%;
            }

            .kata-header,
            .combat-layout,
            .judges,
            .bottom-actions {
                grid-template-columns: 1fr;
            }

            .center-panel {
                grid-template-columns: 1fr 1fr;
            }

            .timer {
                font-size: 4rem;
                min-height: 90px;
            }

            .judges {
                justify-items: center;
            }
        }
    </style>
</head>
<body>
    <main class="kata-screen">
        <section class="kata-board">
            <header class="kata-header">
                <div>Modalidad: <span id="kataModalidadTexto">Kata Individual</span></div>
                <div>Categoría: <span id="kataCategoriaTexto">Masculino 6 -7 años</span></div>
            </header>

            <section class="combat-layout">
                <section class="competidor-col">
                    <article class="competidor competidor-aka">
                        <div class="estado">
                            En Competencia :
                            <strong id="kataCompetidorRojo">**********</strong>
                        </div>
                        <div class="kata-meta">
            <span id="kataLineaRojo" class="kata-select-line">
                                <span class="kata-label">Kata Nro. :</span>
                                <span class="kata-select-wrap">
                                    <input type="search" class="kata-search" data-select="kataSelectRojo" placeholder="Buscar kata..." autocomplete="off">
                                    <select id="kataSelectRojo" class="kata-select" aria-label="Kata rojo">
                                        <option value="">Seleccione</option>
                                        @foreach ($katas as $kata)
                                            @php($numeroKata = $kata->numero_tablero ?: $loop->iteration)
                                            <option value="{{ $kata->id }}" data-numero="{{ $numeroKata }}" data-nombre="{{ $kata->nombre }}" data-search="{{ mb_strtolower($numeroKata . ' ' . $kata->nombre) }}">
                                                {{ $numeroKata }} - {{ $kata->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </span>
                                <button type="button" class="kata-ok" data-select="kataSelectRojo" data-linea="kataLineaRojo" data-numero="kataNumeroRojo" data-nombre="kataNombreRojo">OK</button>
                                <button type="button" class="kata-back" data-select="kataSelectRojo" data-linea="kataLineaRojo" data-numero="kataNumeroRojo" data-nombre="kataNombreRojo" title="Volver a seleccionar">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <strong id="kataNumeroRojo" class="kata-numero-confirmado"></strong>
                            </span>
                            <span>Nombre Kata: <strong id="kataNombreRojo">*****</strong></span>
                        </div>
                        <div id="puntajeRojo" class="puntaje-panel"></div>
                        <div class="proximo">
                            Proximo Combate
                            <span id="kataProximoRojo">**********</span>
                        </div>
                    </article>
                    <div id="banderasRojo" class="banderas-display" aria-label="Banderas rojas"></div>
                    <button type="button" id="btnKikenRojo" class="btn-kiken btn-kiken-aka">Kiken</button>
                </section>

                <section class="center-panel">
                    <div class="timer">00:00</div>
                    <div class="action-row">
                        <button type="button" class="btn-ingreso">Ingreso</button>
                        <button type="button" class="btn-stop">Stop</button>
                        <button type="button" class="btn-inicio">Inicio</button>
                    </div>
                    <button type="button" id="btnBanderas" class="btn-tool">
                        <i class="bi bi-flag-fill"></i>
                        Banderas
                    </button>
                    <button type="button" id="btnPuntos" class="btn-tool">
                        <i class="bi bi-record-circle-fill"></i>
                        Puntos
                    </button>
                    <div id="resultadoWrap" class="resultado-select-wrap">
                        <select id="resultados" name="resultados" class="resultado-select" aria-label="Resultados">
                            <option value="">Resultados</option>
                            <option>Rojo 5 - Azul 0</option>
                            <option>Rojo 4 - Azul 1</option>
                            <option>Rojo 3 - Azul 2</option>
                            <option>Azul 5 – Rojo 0</option>
                            <option>Azul 4 – Rojo 1</option>
                            <option>Azul 3 – Rojo 2</option>
                        </select>
                    </div>
                </section>

                <section class="competidor-col">
                    <article class="competidor competidor-ao">
                        <div class="estado">
                            En Competencia :
                            <strong id="kataCompetidorAzul">**********</strong>
                        </div>
                        <div class="kata-meta">
            <span id="kataLineaAzul" class="kata-select-line">
                                <span class="kata-label">Kata Nro. :</span>
                                <span class="kata-select-wrap">
                                    <input type="search" class="kata-search" data-select="kataSelectAzul" placeholder="Buscar kata..." autocomplete="off">
                                    <select id="kataSelectAzul" class="kata-select" aria-label="Kata azul">
                                        <option value="">Seleccione</option>
                                        @foreach ($katas as $kata)
                                            @php($numeroKata = $kata->numero_tablero ?: $loop->iteration)
                                            <option value="{{ $kata->id }}" data-numero="{{ $numeroKata }}" data-nombre="{{ $kata->nombre }}" data-search="{{ mb_strtolower($numeroKata . ' ' . $kata->nombre) }}">
                                                {{ $numeroKata }} - {{ $kata->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </span>
                                <button type="button" class="kata-ok" data-select="kataSelectAzul" data-linea="kataLineaAzul" data-numero="kataNumeroAzul" data-nombre="kataNombreAzul">OK</button>
                                <button type="button" class="kata-back" data-select="kataSelectAzul" data-linea="kataLineaAzul" data-numero="kataNumeroAzul" data-nombre="kataNombreAzul" title="Volver a seleccionar">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <strong id="kataNumeroAzul" class="kata-numero-confirmado"></strong>
                            </span>
                            <span>Nombre Kata: <strong id="kataNombreAzul">*****</strong></span>
                        </div>
                        <div id="puntajeAzul" class="puntaje-panel"></div>
                        <div class="proximo">
                            Proximo Combate
                            <span id="kataProximoAzul">**********</span>
                        </div>
                    </article>
                    <div id="banderasAzul" class="banderas-display" aria-label="Banderas azules"></div>
                    <button type="button" id="btnKikenAzul" class="btn-kiken btn-kiken-ao">Kiken</button>
                </section>
            </section>

            <section id="juecesKata" class="judges hidden" aria-label="Jueces">
                @foreach ([1, 2, 3, 4, 5] as $judge)
                    <article class="judge">
                        <div class="judge-name">Juez {{ $judge }}</div>
                        <div class="judge-flags">
                            <input type="text" class="judge-score-input judge-score-red" inputmode="decimal" maxlength="4" value="0,0" aria-label="Puntaje rojo juez {{ $judge }}">
                            <input type="text" class="judge-score-input judge-score-blue" inputmode="decimal" maxlength="4" value="0,0" aria-label="Puntaje azul juez {{ $judge }}">
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="bottom-actions">
                <button type="button" id="btnGanadorKata" class="btn-bottom">Ganador</button>
                <button type="button" id="btnNuevoKata" class="btn-bottom">Nuevo</button>
                <button type="button" class="btn-bottom">Anterior</button>
                <button type="button" class="btn-bottom btn-cerrar" onclick="window.location.href='{{ route('dashboard') }}'">
                    Cerrar
                </button>
            </section>
        </section>
    </main>
    <div id="kataToast" class="kata-toast" role="alert" aria-live="assertive"></div>
    <script>
        const puntajeRojo = document.getElementById('puntajeRojo');
        const puntajeAzul = document.getElementById('puntajeAzul');
        const banderasRojo = document.getElementById('banderasRojo');
        const banderasAzul = document.getElementById('banderasAzul');
        const resultados = document.getElementById('resultados');
        const kataToast = document.getElementById('kataToast');
        const tableroKata = @json($tableroKata ?? []);
        const podioKataUrl = @json(route('tablero.kata.podio'));
        const guardarCombateKataUrl = @json(route('tablero.kata.combates.store'));
        const kataStorageKey = `kata-tablero-v3-${tableroKata.sorteo_id || 'default'}-${tableroKata.resultados_version || 0}`;
        let toastTimeout = null;
        let kataState = null;
        let kataCurrentPosition = null;

        function textoKata(valor, fallback = '') {
            return (valor || '').trim() || fallback;
        }

        function cargarNuevoCombateKata() {
            kataState = estadoKata();

            if (!Array.isArray(kataState.llaves) || kataState.llaves.length === 0) {
                document.getElementById('kataModalidadTexto').textContent = textoKata(tableroKata.modalidad, 'Kata Individual');
                document.getElementById('kataCategoriaTexto').textContent = textoKata(tableroKata.categoria, 'Sin categoria pendiente');
                document.getElementById('kataCompetidorRojo').textContent = '********';
                document.getElementById('kataCompetidorAzul').textContent = '********';
                document.getElementById('kataProximoRojo').textContent = '********';
                document.getElementById('kataProximoAzul').textContent = '********';
                return;
            }

            propagarByesKata(kataState.llaves);

            const posicion = siguienteCombateKata(kataState.llaves);

            if (!posicion) {
                kataCurrentPosition = null;
                document.getElementById('kataModalidadTexto').textContent = textoKata(tableroKata.modalidad, 'Kata Individual');
                document.getElementById('kataCategoriaTexto').textContent = textoKata(tableroKata.categoria, 'Sin categoria pendiente');
                document.getElementById('kataCompetidorRojo').textContent = '********';
                document.getElementById('kataCompetidorAzul').textContent = '********';
                document.getElementById('kataProximoRojo').textContent = '********';
                document.getElementById('kataProximoAzul').textContent = '********';
                reiniciarKataSeleccionado('kataSelectRojo', 'kataLineaRojo', 'kataNumeroRojo', 'kataNombreRojo');
                reiniciarKataSeleccionado('kataSelectAzul', 'kataLineaAzul', 'kataNumeroAzul', 'kataNombreAzul');
                limpiarKikenKata();
                resultados.value = '';
                limpiarResultadoBanderas();
                return;
            }

            kataCurrentPosition = posicion;
            const combate = combatePorPosicion(kataState.llaves, posicion) || {};
            const proximo = siguienteCombateKata(kataState.llaves, posicion.indice + 1);
            const proximoCombate = proximo ? combatePorPosicion(kataState.llaves, proximo) : {};
            const rojoCombate = competidorRojoTableroKata(kataState.llaves, posicion, combate);
            const azulCombate = competidorAzulTableroKata(kataState.llaves, posicion, combate);
            const rojoProximo = proximo ? competidorRojoTableroKata(kataState.llaves, proximo, proximoCombate) : null;
            const azulProximo = proximo ? competidorAzulTableroKata(kataState.llaves, proximo, proximoCombate) : null;

            document.getElementById('kataModalidadTexto').textContent = textoKata(tableroKata.modalidad, 'Kata Individual');
            document.getElementById('kataCategoriaTexto').textContent = textoKata(tableroKata.categoria, 'Sin categoria pendiente');
            document.getElementById('kataCompetidorRojo').textContent = textoKata(rojoCombate?.nombre, 'Sin competidor');
            document.getElementById('kataCompetidorAzul').textContent = textoKata(azulCombate?.nombre, 'Sin competidor');
            document.getElementById('kataProximoRojo').textContent = textoKata(rojoProximo?.nombre, 'Sin proximo');
            document.getElementById('kataProximoAzul').textContent = textoKata(azulProximo?.nombre, 'Sin proximo');
            reiniciarKataSeleccionado('kataSelectRojo', 'kataLineaRojo', 'kataNumeroRojo', 'kataNombreRojo');
            reiniciarKataSeleccionado('kataSelectAzul', 'kataLineaAzul', 'kataNumeroAzul', 'kataNombreAzul');
            limpiarKikenKata();
            resultados.value = '';
            limpiarResultadoBanderas();
        }

        function actualizarKataSeleccionado(select, nombreTargetId) {
            const option = select.selectedOptions[0];
            const nombre = option?.dataset.nombre || '';

            document.getElementById(nombreTargetId).textContent = nombre || 'Sin kata';
        }

        function normalizarBusquedaKata(valor) {
            return (valor || '')
                .toString()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .trim();
        }

        function filtrarKatasSelect(input) {
            const select = document.getElementById(input.dataset.select);
            const busqueda = normalizarBusquedaKata(input.value);
            let primerVisible = null;

            if (!select) {
                return;
            }

            select.querySelectorAll('option').forEach(function (option) {
                if (!option.value) {
                    option.hidden = false;
                    option.disabled = false;
                    return;
                }

                const texto = normalizarBusquedaKata(option.dataset.search || option.textContent);
                const visible = !busqueda || texto.includes(busqueda);

                option.hidden = !visible;
                option.disabled = !visible;

                if (visible && !primerVisible) {
                    primerVisible = option;
                }
            });

            if (select.value && select.selectedOptions[0]?.disabled) {
                select.value = '';
            }

            if (!select.value && primerVisible && busqueda) {
                select.value = primerVisible.value;
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }

        function confirmarKataSeleccionado(button) {
            const select = document.getElementById(button.dataset.select);
            const option = select.selectedOptions[0];
            const numero = option?.dataset.numero || '';
            const nombre = option?.dataset.nombre || '';

            if (!numero) {
                mostrarToast('Seleccione un kata primero');
                return;
            }

            document.getElementById(button.dataset.numero).textContent = numero;
            document.getElementById(button.dataset.nombre).textContent = nombre || 'Sin kata';
            document.getElementById(button.dataset.linea).classList.add('is-confirmed');
        }

        function volverASeleccionarKata(button) {
            const select = document.getElementById(button.dataset.select);
            const search = document.querySelector(`.kata-search[data-select="${button.dataset.select}"]`);

            document.getElementById(button.dataset.linea).classList.remove('is-confirmed');
            document.getElementById(button.dataset.numero).textContent = '';
            document.getElementById(button.dataset.nombre).textContent = 'Seleccione';

            if (search) {
                search.closest('.kata-select-wrap')?.classList.add('is-searching');
                search.focus();
                search.select();
            } else {
                select?.focus();
            }
        }

        function reiniciarKataSeleccionado(selectId, lineaId, numeroId, nombreId) {
            const select = document.getElementById(selectId);
            const search = document.querySelector(`.kata-search[data-select="${selectId}"]`);

            select.value = '';
            select.querySelectorAll('option').forEach(function (option) {
                option.hidden = false;
                option.disabled = false;
            });
            if (search) {
                search.value = '';
                search.closest('.kata-select-wrap')?.classList.remove('is-searching');
            }
            document.getElementById(lineaId).classList.remove('is-confirmed');
            document.getElementById(numeroId).textContent = '';
            document.getElementById(nombreId).textContent = 'Seleccione';
        }

        function limpiarKikenKata() {
            document.querySelectorAll('.btn-kiken').forEach(function (button) {
                button.classList.remove('is-active');
            });
        }

        function aplicarResultadoKikenKata(perdedorColor) {
            const rojoPierde = perdedorColor === 'rojo';
            const rojo = rojoPierde ? 0 : 5;
            const azul = rojoPierde ? 5 : 0;

            puntajeRojo.textContent = rojoPierde ? 'Kiken' : rojo;
            puntajeAzul.textContent = rojoPierde ? azul : 'Kiken';
            puntajeRojo.dataset.valor = rojo;
            puntajeAzul.dataset.valor = azul;
            crearBanderas(banderasRojo, rojo, 'roja');
            crearBanderas(banderasAzul, azul, 'azul');
        }

        function estadoKata() {
            if (kataState) {
                return kataState;
            }

            const guardado = sessionStorage.getItem(kataStorageKey);
            kataState = guardado
                ? JSON.parse(guardado)
                : { llaves: JSON.parse(JSON.stringify(tableroKata.llaves || [])) };

            return kataState;
        }

        function guardarEstadoKata() {
            sessionStorage.setItem(kataStorageKey, JSON.stringify(kataState));
        }

        function combatePorPosicion(llaves, posicion) {
            return llaves?.[posicion.roundIndex]?.combates?.[posicion.matchIndex] || null;
        }

        function esFinalKata(llaves, posicion) {
            return Boolean(posicion) && posicion.roundIndex === (llaves.length - 1);
        }

        function competidorRojoTableroKata(llaves, posicion, combate) {
            return esFinalKata(llaves, posicion) ? combate?.b : combate?.a;
        }

        function competidorAzulTableroKata(llaves, posicion, combate) {
            return esFinalKata(llaves, posicion) ? combate?.a : combate?.b;
        }

        function esByeKata(combate) {
            const tieneRojo = Boolean(combate?.a?.nombre);
            const tieneAzul = Boolean(combate?.b?.nombre);

            return Boolean(combate?.bye) && tieneRojo !== tieneAzul;
        }

        function puedePropagarGanadorKata(competidor) {
            const nombre = competidor?.nombre || '';

            return !nombre || nombre.startsWith('Ganador');
        }

        function propagarByesKata(llaves) {
            llaves.forEach(function (ronda, roundIndex) {
                if (!llaves[roundIndex + 1]) {
                    return;
                }

                (ronda.combates || []).forEach(function (combate, matchIndex) {
                    if (!esByeKata(combate)) {
                        return;
                    }

                    const ganador = combate.a || combate.b || null;
                    const nextMatch = Math.floor(matchIndex / 2);
                    const nextSide = matchIndex % 2 === 0 ? 'a' : 'b';

                    if (
                        ganador
                        && llaves[roundIndex + 1]?.combates?.[nextMatch]
                        && puedePropagarGanadorKata(llaves[roundIndex + 1].combates[nextMatch][nextSide])
                    ) {
                        llaves[roundIndex + 1].combates[nextMatch][nextSide] = ganador;
                    }
                });
            });
        }

        function siguienteCombateKata(llaves, desde = 0) {
            let indice = 0;

            for (let roundIndex = 0; roundIndex < llaves.length; roundIndex++) {
                const combates = llaves[roundIndex].combates || [];

                for (let matchIndex = 0; matchIndex < combates.length; matchIndex++) {
                    const combate = combates[matchIndex];
                    const rojo = combate?.a?.nombre || '';
                    const azul = combate?.b?.nombre || '';

                    if (indice >= desde && !combate.realizado && !esByeKata(combate) && rojo && azul) {
                        return { roundIndex, matchIndex, indice };
                    }

                    indice++;
                }
            }

            return null;
        }

        function registrarGanadorKataLocal(colorGanador) {
            const state = estadoKata();

            if (!kataCurrentPosition) {
                cargarNuevoCombateKata();
            }

            const posicion = kataCurrentPosition || siguienteCombateKata(state.llaves);
            const combate = posicion ? combatePorPosicion(state.llaves, posicion) : null;

            if (!combate) {
                return;
            }

            const ganador = colorGanador === 'aka'
                ? competidorRojoTableroKata(state.llaves, posicion, combate)
                : competidorAzulTableroKata(state.llaves, posicion, combate);
            const nextRound = posicion.roundIndex + 1;
            const nextMatch = Math.floor(posicion.matchIndex / 2);
            const nextSide = posicion.matchIndex % 2 === 0 ? 'a' : 'b';

            combate.realizado = true;
            combate.ganador = ganador;

            if (state.llaves[nextRound]?.combates?.[nextMatch]) {
                state.llaves[nextRound].combates[nextMatch][nextSide] = ganador;
            }

            propagarByesKata(state.llaves);
            guardarEstadoKata();
        }

        function calcularPodioKata() {
            const state = estadoKata();
            const llaves = state.llaves || [];
            const podio = { oro: '', plata: '', bronce_1: '', bronce_2: '' };
            const finalRound = llaves.length - 1;
            const final = llaves[finalRound]?.combates?.[0] || null;

            if (final?.ganador?.nombre) {
                podio.oro = final.ganador.nombre;
                podio.plata = final.ganador.id === final.a?.id ? (final.b?.nombre || '') : (final.a?.nombre || '');
            }

            const semifinalRound = llaves.length - 2;
            const bronces = [];

            if (semifinalRound >= 0) {
                (llaves[semifinalRound].combates || []).forEach(function (combate) {
                    if (!combate.ganador?.nombre) {
                        return;
                    }

                    const perdedor = combate.ganador.id === combate.a?.id ? combate.b?.nombre : combate.a?.nombre;

                    if (perdedor) {
                        bronces.push(perdedor);
                    }
                });
            }

            podio.bronce_1 = bronces[0] || '';
            podio.bronce_2 = bronces[1] || '';

            return podio;
        }

        function mostrarPodioKata() {
            const params = tableroKata.sorteo_id
                ? new URLSearchParams({ sorteo_id: tableroKata.sorteo_id })
                : new URLSearchParams({
                    modalidad: tableroKata.modalidad || 'Kata Individual',
                    categoria: tableroKata.categoria || '',
                    ...calcularPodioKata(),
                });

            sessionStorage.removeItem(kataStorageKey);
            window.location.href = `${podioKataUrl}?${params.toString()}`;
        }

        function mostrarToast(mensaje, tipo = 'info') {
            clearTimeout(toastTimeout);
            kataToast.textContent = mensaje;
            kataToast.classList.toggle('is-error', tipo === 'error');
            kataToast.classList.add('visible');
            toastTimeout = setTimeout(function () {
                kataToast.classList.remove('visible');
                kataToast.classList.remove('is-error');
            }, 2200);
        }

        function crearBanderas(contenedor, cantidad, color) {
            contenedor.innerHTML = '';
            contenedor.classList.toggle('visible', cantidad > 0);

            for (let index = 0; index < cantidad; index++) {
                const bandera = document.createElement('span');
                bandera.className = `bandera-mini bandera-${color}`;
                contenedor.appendChild(bandera);
            }
        }

        function limpiarResultadoBanderas() {
            puntajeRojo.textContent = '';
            puntajeAzul.textContent = '';
            puntajeRojo.dataset.valor = '';
            puntajeAzul.dataset.valor = '';
            crearBanderas(banderasRojo, 0, 'roja');
            crearBanderas(banderasAzul, 0, 'azul');
        }

        function mostrarResultadoBanderas(valor) {
            const partes = valor.match(/^(Rojo|Azul)\s+(\d+)\s*[-–]\s+(Rojo|Azul)\s+(\d+)$/i);

            if (!partes) {
                limpiarResultadoBanderas();
                return;
            }

            const primeroColor = partes[1].toLowerCase();
            const primeroPuntos = parseInt(partes[2], 10) || 0;
            const segundoColor = partes[3].toLowerCase();
            const segundoPuntos = parseInt(partes[4], 10) || 0;
            const rojo = primeroColor === 'rojo' ? primeroPuntos : (segundoColor === 'rojo' ? segundoPuntos : 0);
            const azul = primeroColor === 'azul' ? primeroPuntos : (segundoColor === 'azul' ? segundoPuntos : 0);

            puntajeRojo.textContent = rojo;
            puntajeAzul.textContent = azul;
            puntajeRojo.dataset.valor = rojo;
            puntajeAzul.dataset.valor = azul;
            crearBanderas(banderasRojo, rojo, 'roja');
            crearBanderas(banderasAzul, azul, 'azul');
        }

        function numeroDesdeInput(input) {
            const numero = parseFloat(input.value.replace(',', '.'));

            return Number.isNaN(numero) ? 0 : numero;
        }

        function actualizarResultadoJuecesAjax() {
            return new Promise(function (resolve) {
                setTimeout(function () {
                    let rojo = 0;
                    let azul = 0;

                    document.querySelectorAll('.judge').forEach(function (juez) {
                        const rojoInput = juez.querySelector('.judge-score-red');
                        const azulInput = juez.querySelector('.judge-score-blue');
                        const rojoValor = numeroDesdeInput(rojoInput);
                        const azulValor = numeroDesdeInput(azulInput);

                        rojoInput.classList.remove('is-invalid');
                        azulInput.classList.remove('is-invalid');

                        if (rojoValor === azulValor) {
                            return;
                        }

                        if (rojoValor > azulValor) {
                            rojo++;
                        } else if (azulValor > rojoValor) {
                            azul++;
                        }
                    });

                    resolve({ rojo, azul });
                }, 120);
            });
        }

        async function refrescarResultadoJueces() {
            const resultado = await actualizarResultadoJuecesAjax();

            if (resultado.rojo === 0 && resultado.azul === 0) {
                limpiarResultadoBanderas();
                return;
            }

            puntajeRojo.textContent = resultado.rojo;
            puntajeAzul.textContent = resultado.azul;
            puntajeRojo.dataset.valor = resultado.rojo;
            puntajeAzul.dataset.valor = resultado.azul;
            crearBanderas(banderasRojo, resultado.rojo, 'roja');
            crearBanderas(banderasAzul, resultado.azul, 'azul');
        }

        function resetearSiEmpata(input) {
            const juez = input.closest('.judge');
            const rojoInput = juez.querySelector('.judge-score-red');
            const azulInput = juez.querySelector('.judge-score-blue');
            const rojoValor = numeroDesdeInput(rojoInput);
            const azulValor = numeroDesdeInput(azulInput);

            if (rojoValor > 0 && rojoValor === azulValor) {
                input.value = '0,0';
                mostrarToast('NO PUEDE HABER EMPATE EN EL PUNTAJE DEL JUEZ');

                return true;
            }

            return false;
        }

        function datosCompetidor(color) {
            const panel = document.querySelector(`.competidor-${color}`);
            const select = color === 'aka'
                ? document.getElementById('kataSelectRojo')
                : document.getElementById('kataSelectAzul');
            const option = select.selectedOptions[0];

            return {
                nombre: panel.querySelector('.estado strong').textContent.trim(),
                kataNumero: option?.dataset.numero || '',
                kataNombre: option?.dataset.nombre || '',
            };
        }

        function datosResultadoKata(rojo, azul, ganadorColor, ganador) {
            const competidorRojo = datosCompetidor('aka');
            const competidorAzul = datosCompetidor('ao');

            return {
                sorteo_id: tableroKata.sorteo_id || null,
                indice_combate: kataCurrentPosition?.indice ?? 0,
                competidor_rojo: competidorRojo.nombre,
                competidor_azul: competidorAzul.nombre,
                kata_numero_rojo: competidorRojo.kataNumero,
                kata_numero_azul: competidorAzul.kataNumero,
                kata_nombre_rojo: competidorRojo.kataNombre,
                kata_nombre_azul: competidorAzul.kataNombre,
                puntaje_rojo: rojo,
                puntaje_azul: azul,
                kiken_rojo: document.getElementById('btnKikenRojo').classList.contains('is-active'),
                kiken_azul: document.getElementById('btnKikenAzul').classList.contains('is-active'),
                ganador: ganador.nombre,
                ganador_color: ganadorColor === 'aka' ? 'rojo' : 'azul',
            };
        }

        async function guardarResultadoKata(payload) {
            const response = await fetch(guardarCombateKataUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'No se pudo guardar el resultado Kata.');
            }

            return data;
        }

        async function abrirResultadoGanador() {
            const rojo = parseInt(puntajeRojo.dataset.valor || puntajeRojo.textContent, 10) || 0;
            const azul = parseInt(puntajeAzul.dataset.valor || puntajeAzul.textContent, 10) || 0;

            if (rojo === 0 && azul === 0) {
                mostrarToast('Uno de los Competidores tienen que tener puntaje', 'error');
                return;
            }

            const ganadorColor = azul > rojo ? 'ao' : 'aka';
            const ganador = datosCompetidor(ganadorColor);

            try {
                await guardarResultadoKata(datosResultadoKata(rojo, azul, ganadorColor, ganador));
            } catch (error) {
                mostrarToast(error.message || 'No se pudo guardar el resultado Kata.', 'error');
                return;
            }

            registrarGanadorKataLocal(ganadorColor);

            if (!siguienteCombateKata(estadoKata().llaves)) {
                mostrarPodioKata();
                return;
            }

            const params = new URLSearchParams({
                sorteo_id: tableroKata.sorteo_id || '',
                color: ganadorColor === 'aka' ? 'rojo' : 'azul',
                nombre: ganador.nombre,
                kata_numero: ganador.kataNumero,
                kata_nombre: ganador.kataNombre,
                puntaje: ganadorColor === 'aka' ? rojo : azul,
                banderas_rojas: rojo,
                banderas_azules: azul,
                kiken_rojo: document.getElementById('btnKikenRojo').classList.contains('is-active') ? 1 : 0,
                kiken_azul: document.getElementById('btnKikenAzul').classList.contains('is-active') ? 1 : 0,
            });

            window.location.href = `{{ route('tablero.kata.resultado') }}?${params.toString()}`;
        }

        document.getElementById('btnBanderas').addEventListener('click', function () {
            const puntos = document.getElementById('btnPuntos');
            const resultado = document.getElementById('resultadoWrap');
            const jueces = document.getElementById('juecesKata');
            const mostrandoResultados = resultado.classList.contains('visible');

            puntos.style.display = mostrandoResultados ? '' : 'none';
            resultado.classList.toggle('visible', !mostrandoResultados);
            jueces.classList.add('hidden');
            puntos.classList.remove('activo');
            this.classList.toggle('activo', !mostrandoResultados);

            if (mostrandoResultados) {
                resultados.value = '';
                limpiarResultadoBanderas();
            }
        });

        document.getElementById('btnPuntos').addEventListener('click', function () {
            const jueces = document.getElementById('juecesKata');
            const mostrandoJueces = !jueces.classList.contains('hidden');

            jueces.classList.toggle('hidden', mostrandoJueces);
            this.classList.toggle('activo', !mostrandoJueces);

            if (mostrandoJueces) {
                return;
            }

            const primerJuez = jueces.querySelector('.judge-score-input');

            if (primerJuez) {
                primerJuez.focus();
                primerJuez.select();
            }
        });

        document.getElementById('btnGanadorKata').addEventListener('click', abrirResultadoGanador);
        document.getElementById('btnNuevoKata').addEventListener('click', cargarNuevoCombateKata);
        document.getElementById('kataSelectRojo').addEventListener('change', function () {
            actualizarKataSeleccionado(this, 'kataNombreRojo');
        });
        document.getElementById('kataSelectAzul').addEventListener('change', function () {
            actualizarKataSeleccionado(this, 'kataNombreAzul');
        });
        document.querySelectorAll('.kata-select').forEach(function (select) {
            select.addEventListener('focus', function () {
                this.closest('.kata-select-wrap')?.classList.add('is-searching');
            });
        });
        document.querySelectorAll('.kata-search').forEach(function (input) {
            input.addEventListener('input', function () {
                filtrarKatasSelect(this);
            });
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    document.getElementById(this.dataset.select)?.focus();
                }
            });
        });
        document.querySelectorAll('.kata-ok').forEach(function (button) {
            button.addEventListener('click', function () {
                confirmarKataSeleccionado(this);
            });
        });
        document.querySelectorAll('.kata-back').forEach(function (button) {
            button.addEventListener('click', function () {
                volverASeleccionarKata(this);
            });
        });
        document.querySelectorAll('.btn-kiken').forEach(function (button) {
            button.addEventListener('click', function () {
                const otroKiken = this.id === 'btnKikenRojo'
                    ? document.getElementById('btnKikenAzul')
                    : document.getElementById('btnKikenRojo');

                if (!this.classList.contains('is-active') && otroKiken?.classList.contains('is-active')) {
                    mostrarToast('Solo se puede dar Kiken a un competidor', 'error');
                    return;
                }

                this.classList.toggle('is-active');

                if (this.classList.contains('is-active')) {
                    aplicarResultadoKikenKata(this.id === 'btnKikenRojo' ? 'rojo' : 'azul');
                } else {
                    limpiarResultadoBanderas();
                }
            });
        });

        resultados.addEventListener('change', function () {
            mostrarResultadoBanderas(this.value);
        });

        document.querySelectorAll('.judge-score-input').forEach(function (input) {
            input.addEventListener('input', function () {
                let value = this.value
                    .replace(/\./g, ',')
                    .replace(/[^\d,]/g, '');
                const parts = value.split(',');
                const entero = (parts[0] || '').slice(0, 2);
                const decimal = parts.length > 1 ? parts.slice(1).join('').slice(0, 1) : '';

                value = entero;

                if (parts.length > 1) {
                    value += ',' + decimal;
                }

                if (value !== '' && value !== ',') {
                    const numeric = parseFloat(value.replace(',', '.'));

                    if (!Number.isNaN(numeric) && numeric > 10.9) {
                        value = '10,9';
                    }
                }

                this.value = value;
                refrescarResultadoJueces();
            });

            input.addEventListener('blur', function () {
                if (this.value === '' || this.value === ',') {
                    return;
                }

                let numeric = parseFloat(this.value.replace(',', '.'));

                if (Number.isNaN(numeric) || numeric < 0) {
                    numeric = 0;
                }

                numeric = Math.min(10.9, numeric);
                this.value = numeric.toFixed(1).replace('.', ',');

                resetearSiEmpata(this);

                refrescarResultadoJueces();
            });
        });

        cargarNuevoCombateKata();
    </script>
</body>
</html>
