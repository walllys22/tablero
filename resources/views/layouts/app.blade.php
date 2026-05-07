<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <title>@yield('title', 'Kaiteki')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        #sidebar {
            transition: all 0.3s;
            min-width: 200px;
            max-width: 200px;
            background-color: #343a40;
        }
        #sidebar.collapsed {
            min-width: 80px;
            max-width: 80px;
        }
        #sidebar.collapsed .sidebar-text {
            display: none;
        }
        #sidebar .nav-link i,
        #sidebar .nav-link img {
            margin-right: 0.5rem;
        }
        #sidebar .nav-link:hover,
        #sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.12);
        }
        #sidebar.collapsed .nav-link {
            text-align: center;
            padding: 10px 0;
        }
        #sidebar.collapsed .nav-link i,
        #sidebar.collapsed .nav-link img {
            margin-right: 0;
        }
        body {
            min-height: 100vh;
        }
        .bottom-bar {
            position: sticky;
            bottom: 0;
            width: 100%;
            background: #ffffff;
            border-top: 1px solid #dee2e6;
            padding: 0.75rem 1rem;
            box-shadow: 0 -1px 6px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }
        .bottom-bar .btn {
            min-width: 100px;
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <div class="d-flex flex-grow-1">
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
                    <a href="{{ route('people.browse') }}" class="nav-link text-white {{ request()->routeIs('people.browse') ? 'active' : '' }} d-flex align-items-center">
        
                        <span class="sidebar-text">Persona</span>
                    </a>
                </li>
                <li class="nav-item" style="margin-top: 15px; margin-bottom: 10px;">
                    <a href="{{ route('tablero.kumite') }}" class="nav-link text-white {{ request()->routeIs('tablero.kumite') ? 'active' : '' }} d-flex align-items-center">
                        <img src="{{ asset('images/kumite.png') }}" alt="Kumite" class="me-2" style="height: 24px;">
                        <span class="sidebar-text">Kumite</span>
                    </a>
                </li>
                <li class="nav-item" style="margin-top: 10px; margin-bottom: 15px;">
                    <a href="{{ route('tablero.kata') }}" class="nav-link text-white {{ request()->routeIs('tablero.kata') ? 'active' : '' }} d-flex align-items-center">
                        <img src="{{ asset('images/kata.png') }}" alt="Kata" class="me-2" style="height: 24px;">
                        <span class="sidebar-text">Kata</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="px-4 pb-4 pt-0 flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded-3 mb-4">
                <div class="container-fluid px-3">
                    <button class="btn btn-link text-dark d-lg-none p-0 me-3" type="button" id="toggleSidebarMobile">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <span class="navbar-brand mb-0 h1">@yield('title', 'Kaiteki')</span>
                    <div class="collapse navbar-collapse justify-content-end">
                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('people.browse') }}">Personas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('tablero.kumite') }}">Kumite</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('tablero.kata') }}">Kata</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            @yield('content')
            <div class="bottom-bar d-flex justify-content-between align-items-center mt-4">
                <div>
                    <strong>Kaiteki - Eventos </strong> · Versión 1.0
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
        document.getElementById('toggleSidebarMobile')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    </script>
    @stack('scripts')
</body>
</html>
