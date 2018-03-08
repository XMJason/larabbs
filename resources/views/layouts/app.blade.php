<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <?php // csrf-token 标签是为了方便前端 JavaScript 脚本获取 CSRF 令牌 ?>
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'LaraBBS') - Laravel 进阶教程</title>

        <!-- Styles -->
        <?php // asset('css/app.css') 使用当前请求的协议（HTTP 或 HTTPS）为资源文件生成一个URL ?>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @yield('styles')
    </head>
    <body>
        <?php // route_class() 是自定义的辅助方法 ?>
        <div id="app" class="{{ route_class() }}-page">
            @include('layouts._header')

            <div class="container">
                @include('layouts._message')
                @yield('content')
            </div>

            @include('layouts._footer')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        @yield('scripts')
    </body>
</html>
