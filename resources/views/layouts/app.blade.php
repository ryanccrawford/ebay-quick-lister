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

    /* XLSF 2007 */

body {
 /* background:#333 url(images/bg-strip-dark.png) 0px 0px;
 font-family:normal,"Century Gothic","Helvetica Neue Light","Helvetica Neue",georgia,"times new roman","Arial Rounded MT Bold",helvetica,verdana,tahoma,arial,"sans serif"; */
 /* font-size:75%;
 color:#666; */
}

h1, h1 a {
 color:#999;
 text-decoration:none;
}

h1 {
 color:#999;
 margin-bottom:0;
 margin-left:-5px;
 margin-top:0;
 padding-left:5px;
 padding-right:5px;
}

h1, h2, h3 {
 clear:both;
 float:left;
 font-family:normal,"Century Gothic","Helvetica Neue Light","Helvetica Neue",georgia,"times new roman","Arial Rounded MT Bold",helvetica,verdana,tahoma,arial,"sans serif";
 font-size:3em;
 font-size-adjust:none;
 margin-bottom:0.25em;
 padding-bottom:1px;
}

h1, h2 {
 letter-spacing:-1px;
 margin-bottom:0;
 margin-left:-5px;
 margin-top:0;
 padding-left:5px;
 padding-right:5px;
}

a {
 color:#6699cc;
 padding:0px 2px;
 text-decoration:none;
}

a:hover {
 background:#6699cc;
 color:#fff;
}

#lights {
 position:absolute;
 left:0px;
 top:51px;
 width:100%;
 height:100%;
 overflow:hidden;
}

.xlsf-light {
 position:absolute;
}

body.fast .xlsf-light {
 opacity:0.9;
}

.xlsf-fragment {
 position:absolute;
 background:transparent url(images/bulbs-50x50-fragments.png) no-repeat 0px 0px;
 width:50px;
 height:50px;
}

.xlsf-fragment-box {
 position:absolute;
 left:0px;
 top:0px;
 width:50px;
 height:50px;
 *width:100%;
 *height:100%;
 display:none;
}

.xlsf-cover {
 position:fixed;
 left:0px;
 top:0px;
 width:100%;
 height:100%;
 background:#fff;
 opacity:1;
 z-index:999;
 display:none;
}

/*
.xlsf-light.bottom {
 height:49px;
 border-bottom:1px solid #006600;
}

.xlsf-light.top {
 height:49px;
 border-top:1px solid #009900;
}
*/
.nav-item{
    z-index: 100;
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
@stack('end')
                 @yield('last')

                <script src="{{ asset('js/ckeditor/ckeditor.js') }}"></script>
                <script src="{{ asset('js/ckeditor/adapters/jquery.js') }}"></script>
                <script src="{{ asset('js/jscode.js') }}"></script>



</body>
</html>
