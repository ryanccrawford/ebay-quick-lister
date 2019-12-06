<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->
    <script>
        const currentPath = (location.pathname+location.search).substr(1);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/4.0.2/echarts-en.min.js" charset="utf-8"></script>
    <!-- <script src="{{ asset('js/manifest.js')}}"></script>
    <script src="{{ asset('js/vendor.js')}}"></script> -->
    <script src="{{ asset('js/app.js')}}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('head')
    <style>
    #debugdumps span{
        font-size: 14px !important;

    }
    
    </style>
</head>
<body class="bg-info">
    <div class="fluid-container" id="debugdumps">

    </div>
    <div id="app">
        @include('layouts.partials.nav-bar')
        <main class="py-4">
            @yield('content')
        </main>
    </div>

                 @yield('last')
                <script src="{{ asset('js/ckeditor/ckeditor.js')}}"></script>
                <script src="{{ asset('js/ckeditor/adapters/jquery.js')}}"></script>
                <script src="{{ asset('js/jscode.js')}}"></script>
    @stack('end')

</body>
</html>
