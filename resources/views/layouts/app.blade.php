<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="app-body {{ auth()->check() ? 'app-shell' : '' }}">
    @auth
        @include('layouts.navigation')

        <div class="app-shell-body">
            @include('layouts.sidebar')

            <div class="app-content">
                @isset($header)
                    <header class="page-header">
                        <div class="container-fluid py-4">
                            {{ $header }}
                        </div>
                    </header>
                @elseif (View::hasSection('header'))
                    <header class="page-header">
                        <div class="container-fluid py-4">
                            @yield('header')
                        </div>
                    </header>
                @endif

                <main class="app-main">
                    {{ $slot ?? '' }}
                    @yield('content')
                </main>
            </div>
        </div>

        @include('layouts.bottom-navigation')
    @else
        @hasSection('header')
            <header class="page-header">
                <div class="container py-4">
                    @yield('header')
                </div>
            </header>
        @endif

        <main class="py-4">
            <div class="container">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </main>
    @endauth

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>
