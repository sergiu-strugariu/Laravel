<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <meta name="theme-color" content="#6777ef"/>
        <link rel="apple-touch-icon" href="{{ asset('logo.PNG') }}">
        <link rel="manifest" href="{{ asset('/manifest.json') }}">
        <script src="{{ asset('/sw.js') }}"></script>

        <!-- Scripts -->
        @vite(['resources/js/service-worker.js'])

    </head>
    <body class="font-sans antialiased">
        <img  src="{{ asset("public/IMG_1471.jpeg") }}" alt="sasdas">
    </body>
</html>
