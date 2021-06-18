<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        
        <title>StickyNote Mount</title>
    </head>
    <body>
        <div id="app">

            <work-top></work-top>

        </div>

        <script>
            // console.log(window);
            if (window.laravel === undefined) {
                window.laravel = {};
            }
            window.laravel.user = {!! Auth::user() !!};
            window.laravel.asset = "{!! asset('') !!}";
        </script>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS, then Font Awesome -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        <script defer src="https://use.fontawesome.com/releases/v5.7.2/js/all.js"></script>        
        
        <!--
        <script src="{{ asset('/js/lib/zlib/zip.min.js') }}"></script>
        <script src="{{ asset('/js/lib/zlib/unzip.min.js') }}"></script>
        -->
        
        <script src="{{ asset('/js/lib/jszip/jszip.min.js') }}"></script>
        
        <script src="{{ asset('/js/app.js') }}"></script>
    </body>
</html>