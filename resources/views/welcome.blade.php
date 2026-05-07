<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <title>Kaiteki - Eventos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="d-flex flex-grow-1">
        <!-- Este es tu Sidebar -->
        <div id="sidebar" class="bg-dark text-white p-3 flex-shrink-0">
            <div class="d-flex align-items-center justify-content-between mb-3"> 
                <div class="d-flex align-items-center">
                    <img src="{{ asset('images/icono.png') }}" alt="Kaiteki Icon" class="img-fluid me-2" style="height: 40px;">
                    <h4 class="m-0 sidebar-text">Kaiteki</h4>
                </div>
                <button id="toggleSidebar" class="btn btn-outline-light border-0">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            <hr class="sidebar-text">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item" style="margin-top: 15px; margin-bottom: 10px;">
                    <a href="{{ route('tablero.kumite') }}" class="nav-link text-white active d-flex align-items-center">
                        <img src="{{ asset('images/kumite.png') }}" alt="Kumite" class="me-2" style="height: 24px;">
                        <span class="sidebar-text">Kumite</span>
                    </a>
                </li>
                <li class="nav-item" style="margin-top: 10px; margin-bottom: 15px;">
                    <a href="{{ route('tablero.kata') }}" class="nav-link text-white active d-flex align-items-center">
                        <img src="{{ asset('images/kata.png') }}" alt="Kumite" class="me-2" style="height: 24px;">
                        <span class="sidebar-text">kata</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="px-4 pb-4 pt-0 flex-grow-1">
            <div class="mt-0">
                <img src="{{ asset('images/tablero.png') }}" 
                    alt="Tablero de Control" 
                    class="img-fluid rounded shadow" 
                    style="width: 100%; display: block; margin: 0 auto;">
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
</body>
    <style>
        #sidebar {
            transition: all 0.3s;
            min-width: 200px;
            max-width: 200px;
        }
        #sidebar.collapsed {
            min-width: 80px;
            max-width: 80px;
        }
        #sidebar.collapsed .sidebar-text {
            display: none;
        }
        #sidebar .nav-link i {
            font-size: 1.2rem;
        }
        #sidebar.collapsed .nav-link {
            text-align: center;
            padding: 10px 0;
        }
        #sidebar.collapsed .nav-link i,
        #sidebar.collapsed .nav-link img {
            margin-right: 0;
        }
    </style>
</html>