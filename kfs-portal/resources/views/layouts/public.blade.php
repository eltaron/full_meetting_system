<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F6E51">
    <meta name="description" content="@yield('meta_description', 'البوابة الإلكترونية الموحدة لمحافظة كفر الشيخ — الأرشيف الإلكتروني، الاجتماعات، والتكليفات والمتابعة في منظومة واحدة.')">

    <title>@yield('title', 'بوابة محافظة كفر الشيخ')</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">

    <link rel="stylesheet" href="{{ asset('css/kfs-royal.css') }}">
    @stack('styles')
</head>
<body class="kfs">
    <a href="#main" class="kfs-sr-only">تخطَّ إلى المحتوى الرئيسي</a>

    @hasSection('header')
        @yield('header')
    @else
        @include('partials.site-header')
    @endif

    <main id="main">
        @yield('content')
    </main>

    @hasSection('footer')
        @yield('footer')
    @else
        @include('partials.site-footer')
    @endif

    <script>
        (function () {
            var burger = document.querySelector('[data-kfs-burger]');
            var nav = document.getElementById('kfs-site-nav');
            if (!burger || !nav) return;
            burger.addEventListener('click', function () {
                var open = nav.classList.toggle('is-open');
                burger.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
