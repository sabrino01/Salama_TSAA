<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Salama_tsaa</title>
        <link rel="shortcut icon" href="image/salama.png" type="image/x-icon">
        @if(app()->environment('local'))
            <script type="module" src="http://localhost:5173/@vite/client"></script>
            <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
            <link rel="stylesheet" href="http://localhost:5173/resources/css/app.css">
        @else
            <script type="module" src="{{ asset('build/assets/app.js') }}"></script>
            <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
        @endif
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
