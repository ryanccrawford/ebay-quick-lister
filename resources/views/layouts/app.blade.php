<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->

    <title>{{ config('app.name') }}</title>

    <!-- Scripts -->


    <script src="{{ asset('js/manifest.js')}}"></script>
    <script src="{{ asset('js/vendor.js')}}"></script>
    <script src="{{ asset('js/app.js')}}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('head')
</head>
<body class="bg-info">
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
