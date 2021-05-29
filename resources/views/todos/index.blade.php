<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <link rel="stylesheet" href="{{ asset('/css/main.css') }}">

        <title>Todos</title>
    </head>
    <body>
        <p>name = {{ Auth::user()->name }}, id = {{ Auth::id() }}</p>
        
        <div id="app">

        <todos-list></todos-list>
        <rect-test></rect-test>
        <rect-test-2></rect-test-2>

        </div>

        <script>
            // console.log(window);
            if (window.laravel === undefined) {
                window.laravel = {};
            }
            window.laravel.user = {!! Auth::user() !!};
            window.laravel.asset = "{!! asset('') !!}";
        </script>

        <script src="{{ asset('/js/app.js') }}"></script>
    </body>
</html>