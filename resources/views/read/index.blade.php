<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>StickyNote Mount ReadOnly</title>
        
        <style type="text/css">
            .menu-bar-class {
                position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
                width:  1800px;
                height: 30px;
                border: 1px solid #000;
                background-color: #ffffff;
                margin: 40px 40px 2px;
                padding: 0;
            }
            
            .menu-bar-content-class {
                display: inline-block;
            }
            
            .menu-bar-content-space-class {
                display: inline-block;
                width: 20px;
            }
        </style>        
    </head>
    <body>
        <div class="menu-bar-class">
            <div class="menu-bar-content-class">StickyNote</div>
            @if (Auth::check())
                {{-- ユーザがログインしているとき --}}

                {{-- 編集ページへのリンク --}}
                <div class="menu-bar-content-space-class"></div>
                <div class="menu-bar-content-class">{!! link_to('/work', 'Edit') !!}</div>
                
                {{-- ログアウトへのリンク --}}
                <div class="menu-bar-content-space-class"></div>
                <div class="menu-bar-content-class">{!! link_to_route('logout.get', 'Logout') !!}</div>
            @endif
        </div>
        
        <div id="app">

        <read-mount></read-mount>

        </div>

        <script>
            // console.log(window);
            if (window.laravel === undefined) {
                window.laravel = {};
            }
            window.laravel.asset = "{!! asset('') !!}";
        </script>
        
        <script src="{{ asset('/js/app.js') }}"></script>
    </body>
</html>