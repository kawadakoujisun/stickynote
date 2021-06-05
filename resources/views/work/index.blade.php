<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>StickyNote Mount</title>
    </head>
    <body>
        <div id="app">

        <work-menu-bar></work-menu-bar>
        <work-mount></work-mount>

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