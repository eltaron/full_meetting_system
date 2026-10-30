<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'خطأ') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/kfs-royal.css') }}">
</head>
<body class="kfs">
    <div class="kfs-status-page">
        <div class="kfs-status-card">
            <div class="kfs-status-card__icon">
                @yield('icon')
            </div>

            <span class="kfs-eyebrow" style="justify-content:center; display:inline-flex;">@yield('code', 'خطأ')</span>
            <h1 class="kfs-mt-2">@yield('heading')</h1>
            <p>@yield('message')</p>

            <div class="kfs-status-card__actions">
                @auth
                    <a href="{{ route('portal.dashboard') }}" class="kfs-btn">العودة إلى البوابة</a>
                @else
                    <a href="{{ url('/') }}" class="kfs-btn">الصفحة الرئيسية</a>
                    <a href="{{ route('login') }}" class="kfs-btn kfs-btn--ghost">تسجيل الدخول</a>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
