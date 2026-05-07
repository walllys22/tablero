<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tablero Kata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 h-screen flex flex-col overflow-hidden">

    <!-- Contenedores Superiores -->
    <div class="flex-1 flex w-full">
        <!-- Lado Izquierdo (AZUL) -->
        <div class="flex-1 bg-blue-700 flex flex-col items-center justify-center border-r-8 border-white">
            <span class="text-white text-5xl font-black mb-6 uppercase tracking-widest text-center">EN COMPETENCIA</span>
            <span class="text-white text-7xl font-bold text-center">-----*-----</span>
        </div>

        <!-- Centro (Temporizador) -->
        <div class="w-1/3 bg-white flex flex-col items-center justify-center">
            <div id="container-timer" class="bg-yellow-400 w-full py-16 rounded-3xl shadow-xl border-4 border-gray-200 text-center mb-10">
                <span id="timer-display" class="text-9xl font-mono font-bold text-gray-900">00:00</span>
            </div>
            <div class="grid grid-cols-2 gap-4 w-full px-4">
                <button id="btn-inicio" onclick="iniciarTiempo(35); toggleTimerButtons('inicio')" class="bg-green-600 text-white py-4 rounded-xl font-black text-2xl hover:bg-green-700 transition-transform active:scale-95 shadow-lg uppercase">Inicio</button>
                <button id="btn-stop" onclick="detenerTiempo()" class="hidden bg-yellow-600 text-white py-4 rounded-xl font-black text-2xl hover:bg-yellow-700 transition-transform active:scale-95 shadow-lg uppercase">Stop</button>
                <button id="btn-fin" onclick="iniciarTiempo(300); toggleTimerButtons('fin')" class="hidden bg-red-600 text-white py-4 rounded-xl font-black text-2xl hover:bg-red-700 transition-transform active:scale-95 shadow-lg uppercase">Fin</button>
                <button id="btn-cerrar" onclick="window.history.back()" class="bg-gray-600 text-white py-4 rounded-xl font-black text-2xl hover:bg-gray-700 transition-transform active:scale-95 shadow-lg uppercase">Cerrar</button>
            </div>
        </div>

        <!-- Lado Derecho (ROJO) -->
        <div class="flex-1 bg-red-700 flex flex-col items-center justify-center border-l-8 border-white">
            <span class="text-white text-5xl font-black mb-6 uppercase tracking-widest text-center">EN COMPETENCIA</span>
            <span class="text-white text-7xl font-bold text-center">-----*-----</span>
        </div>
    </div>

    <!-- Contenedor Inferior (15% de alto) -->
    <div class="h-[20%] w-full bg-white border-t-8 border-gray-300 flex items-center justify-around px-8">
        @for ($i = 1; $i <= 5; $i++)
        <div class="flex flex-col items-center">
            <div class="flex items-center gap-3 mb-2">
                <span class="font-black text-gray-800 text-xl uppercase">JUEZ {{ $i }}</span>
                <button onclick="generarQR({{ $i }})" class="bg-slate-800 text-white p-2 rounded-lg hover:bg-black transition-colors shadow-md">
                    <i class="fas fa-qrcode"></i>
                </button>
            </div>
            <div class="flex gap-2">
                <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="w-20 bg-blue-700 border-2 border-blue-800 text-center py-2 rounded-xl text-xl font-bold text-white shadow-inner focus:outline-none focus:ring-4 focus:ring-blue-400/50 transition-all" placeholder="0.0">
                <input type="number" step="0.1" min="0" max="10" onfocus="this.select()" class="w-20 bg-red-700 border-2 border-red-800 text-center py-2 rounded-xl text-xl font-bold text-white shadow-inner focus:outline-none focus:ring-4 focus:ring-red-400/50 transition-all" placeholder="0.0">
            </div>
        </div>
        @endfor
    </div>

    <!-- Modal para mostrar el QR -->
    <div id="modal-qr" class="fixed inset-0 bg-black/90 hidden z-50 flex flex-col items-center justify-center">
        <div class="bg-white p-10 rounded-[40px] shadow-2xl flex flex-col items-center">
            <h2 id="qr-title" class="text-3xl font-black text-gray-900 mb-8 uppercase italic">Acceso Juez</h2>
            <div id="qrcode" class="p-4 bg-white border-4 border-gray-100 rounded-xl"></div>
            <button onclick="cerrarModal()" class="mt-10 bg-red-600 text-white px-12 py-3 rounded-full font-black text-xl hover:bg-red-700 shadow-xl transition-all">CERRAR</button>
        </div>
    </div>

    <script>
        let timerInterval = null;
        let tiempoRestante = 0;
        let qrcodeObject = null;

        // Inicializar objeto QR al cargar
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
            // Detener cualquier intervalo previo
            clearInterval(timerInterval);
            
            tiempoRestante = segundos;
            actualizarDisplay(tiempoRestante);

            timerInterval = setInterval(() => {
                if (tiempoRestante <= 0) {
                    clearInterval(timerInterval);
                    
                    // Si terminó el tiempo de inicio (35s), pasar automáticamente al tiempo de fin (300s)
                    if (segundos === 35) {
                        toggleTimerButtons('fin');
                        iniciarTiempo(300);
                    } else {
                        document.getElementById('btn-cerrar').classList.remove('hidden');
                    }
                    return;
                }
                tiempoRestante--;
                actualizarDisplay(tiempoRestante);
            }, 1000);
        }

        function detenerTiempo() {
            clearInterval(timerInterval);
            document.getElementById('btn-cerrar').classList.remove('hidden');
        }

        function actualizarDisplay(segundos) {
            const min = Math.floor(segundos / 60).toString().padStart(2, '0');
            const seg = (segundos % 60).toString().padStart(2, '0');
            document.getElementById('timer-display').innerText = `${min}:${seg}`;
        }

        function toggleTimerButtons(estado) {
            if (estado === 'inicio') {
                document.getElementById('btn-inicio').classList.add('hidden');
                document.getElementById('btn-fin').classList.remove('hidden');
                document.getElementById('btn-stop').classList.add('hidden');
                document.getElementById('btn-cerrar').classList.add('hidden');
            } else if (estado === 'fin') {
                document.getElementById('btn-fin').classList.add('hidden');
                document.getElementById('btn-stop').classList.remove('hidden');
                document.getElementById('btn-cerrar').classList.add('hidden');
            }
        }

        function generarQR(juezId) {
            const modal = document.getElementById('modal-qr');
            const titulo = document.getElementById('qr-title');
            
            titulo.innerText = `JUEZ ${juezId}`;
            
            // Generar contenido único para el QR (ejemplo con ID y marca de tiempo)
            const urlAcceso = `${window.location.origin}/calificar/juez/${juezId}?t=${Date.now()}`;
            
            qrcodeObject.clear();
            qrcodeObject.makeCode(urlAcceso);
            
            modal.classList.remove('hidden');
        }

        function cerrarModal() {
            document.getElementById('modal-qr').classList.add('hidden');
        }

        // Cerrar modal al presionar Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') cerrarModal();
        });
    </script>

    <style>
        #timer-display { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }

        /* Ocultar flechas de control en inputs numéricos */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        input[type=number] { -moz-appearance: textfield; }
    </style>
</body>
</html>