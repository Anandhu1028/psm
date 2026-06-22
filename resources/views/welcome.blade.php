<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'TIMS CRO Performance Management System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <main class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="fw-bold mb-3">{{ config('app.name', 'TIMS CRO Performance Management System') }}</h1>
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-ai-assistant">Open Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-ai-assistant">Login</a>
            @endauth
        </div>
    </main>
</body>
</html>
