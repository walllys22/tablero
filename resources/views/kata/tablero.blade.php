<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
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
            font-size: clamp(1.45rem, 2.25vw, 2.45rem);
            font-weight: 700;
            gap: 28px;
            grid-template-columns: 1fr 1fr;
            line-height: 1.1;
            min-height: 62px;
            padding: 10px 18px;
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

        .kata-toast {
            background: #ffff00;
            border: 2px solid #000000;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .28);
            color: #000000;
            display: none;
            font-size: 1rem;
            font-weight: 700;
            left: 50%;
            min-width: 320px;
            padding: 12px 18px;
            position: fixed;
            text-align: center;
            top: 18px;
            transform: translateX(-50%);
            z-index: 9999;
        }

        .kata-toast.visible {
            display: block;
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
                <div>Modalidad: Kata Individual</div>
                <div>Categoría: Masculino 6 -7 años</div>
            </header>

            <section class="combat-layout">
                <section class="competidor-col">
                    <article class="competidor competidor-aka">
                        <div class="estado">
                            En Competencia :
                            <strong>Walter Landivar Limpias</strong>
                        </div>
                        <div class="kata-meta">
                            <span>Kata Nro. : 1</span>
                            <span>Nombre Kata: Anan</span>
                        </div>
                        <div id="puntajeRojo" class="puntaje-panel"></div>
                        <div class="proximo">
                            Proximo Combate
                            <span>Luis Sillo Mayuco</span>
                        </div>
                    </article>
                    <div id="banderasRojo" class="banderas-display" aria-label="Banderas rojas"></div>
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
                            <strong>Alfredo Taraune Becerra</strong>
                        </div>
                        <div class="kata-meta">
                            <span>Kata Nro. : 2</span>
                            <span>Nombre Kata: Anan Dai</span>
                        </div>
                        <div id="puntajeAzul" class="puntaje-panel"></div>
                        <div class="proximo">
                            Proximo Combate
                            <span>Gustavo Roca Nacif</span>
                        </div>
                    </article>
                    <div id="banderasAzul" class="banderas-display" aria-label="Banderas azules"></div>
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
                <button type="button" class="btn-bottom">Nuevo</button>
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
        let toastTimeout = null;

        function mostrarToast(mensaje) {
            clearTimeout(toastTimeout);
            kataToast.textContent = mensaje;
            kataToast.classList.add('visible');
            toastTimeout = setTimeout(function () {
                kataToast.classList.remove('visible');
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

            return {
                nombre: panel.querySelector('.estado strong').textContent.trim(),
                kataNumero: panel.querySelector('.kata-meta span:nth-child(1)').textContent.replace('Kata Nro. :', '').trim(),
                kataNombre: panel.querySelector('.kata-meta span:nth-child(2)').textContent.replace('Nombre Kata:', '').trim(),
            };
        }

        function abrirResultadoGanador() {
            const rojo = parseInt(puntajeRojo.textContent, 10) || 0;
            const azul = parseInt(puntajeAzul.textContent, 10) || 0;
            const ganadorColor = azul > rojo ? 'ao' : 'aka';
            const ganador = datosCompetidor(ganadorColor);
            const params = new URLSearchParams({
                color: ganadorColor === 'aka' ? 'rojo' : 'azul',
                nombre: ganador.nombre,
                kata_numero: ganador.kataNumero,
                kata_nombre: ganador.kataNombre,
                puntaje: ganadorColor === 'aka' ? rojo : azul,
                banderas_rojas: rojo,
                banderas_azules: azul,
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
    </script>
</body>
</html>
