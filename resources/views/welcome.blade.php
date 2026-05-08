<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Torneos') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icono.png') }}">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="app-body">
    <main class="auth-shell">
        <div class="auth-card text-center">
            <img src="{{ asset('images/icono.png') }}" alt="{{ config('app.name', 'Torneos') }}" class="app-logo mb-3">
            <h1 class="h4 mb-3">{{ config('app.name', 'Torneos') }}</h1>
            <a href="{{ route('login') }}" class="btn btn-primary">Ingresar</a>
        </div>
    </main>
</body>
</html>
