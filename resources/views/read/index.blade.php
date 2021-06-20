<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>StickyNote Mount ReadOnly</title>
        
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap" rel="stylesheet">
        
        <style type="text/css">
            .menu-bar-class {
                position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
                width:  100%;
                height: 30px;
                /*border: 1px solid #000;*/
                background-color: #ffffff;
                margin: 20px auto 0px;
                padding: 0;
            }
            
            @media (min-width: 576px) {
                /* 画面の横幅が576px以上のとき */
                .menu-bar-class {
                    position: relative;  /* 子要素の位置を親基準にしたかったので、親であるこれのpositionはstatic以外を指定しておく。 */
                    width:  1800px;
                    height: 30px;
                    /*border: 1px solid #000;*/
                    background-color: #ffffff;
                    margin: 20px 20px 0px;
                    padding: 0;
                }
            }
            
            .menu-bar-mark-class {
                display: inline-block;
                vertical-align: middle;
                padding: 0;
            }
            
            .menu-bar-content-class {
                display: inline-block;
                padding: 0px 10px;
                line-height: 30px;
            }
            
            .menu-bar-content-space-class {
                display: inline-block;
                width: 20px;
            }

            .menu-bar-content-title-class {
                font-family: 'Kosugi Maru', sans-serif;
            }
        </style>        
    </head>
    <body>
        <div class="menu-bar-class">
            <div class="menu-bar-mark-class"><img src="{{ asset('/images/stickynote_mark.jpg') }}"></div>
            
            <div class="menu-bar-content-class menu-bar-content-title-class">StickyNote</div>
            
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