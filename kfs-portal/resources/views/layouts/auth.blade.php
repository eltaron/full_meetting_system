<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F6E51">
    <title>@yield('title', 'تسجيل الدخول') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/kfs-royal.css') }}">
    @stack('styles')
</head>
<body class="kfs">
    <div class="kfs-auth">
        {{-- الجانب التعريفي --}}
        <aside class="kfs-auth__aside">
            <x-kfs.brand light />

            <div>
                <h2>@yield('aside_title', 'بوابة واحدة تجمع') <em>@yield('aside_highlight', 'كل أنظمتك الإدارية')</em></h2>
                <p>@yield('aside_lead', 'سجّل الدخول مرة واحدة للوصول إلى الأرشيف الإلكتروني، والاجتماعات، والتكليفات — حسب التصريح الممنوح لحسابك.')</p>

                <ul class="kfs-auth__points">
                    <li>
                        <x-kfs.icon name="shield" :size="20" />
                        <span>صلاحيات دقيقة لكل نظام على حدة</span>
                    </li>
                    <li>
                        <x-kfs.icon name="layers" :size="20" />
                        <span>بيانات موحّدة بين الأنظمة الثلاثة</span>
                    </li>
                    <li>
                        <x-kfs.icon name="lock" :size="20" />
                        <span>جلسة آمنة وإدارة كاملة للحساب</span>
                    </li>
                </ul>
            </div>

            <div class="kfs-auth__foot">
                © {{ date('Y') }} محافظة كفر الشيخ — المنظومة الإدارية الموحدة
            </div>
        </aside>

        {{-- نموذج الدخول --}}
        <main class="kfs-auth__main">
            <div class="kfs-auth-card">
                <a href="{{ url('/') }}" class="kfs-row kfs-small kfs-text-muted" style="text-decoration:none;">
                    <x-kfs.icon name="arrow-right" :size="16" />
                    العودة إلى الصفحة الرئيسية
                </a>

                @if (session('status'))
                    <div class="kfs-alert kfs-alert--success">
                        <x-kfs.icon name="check-circle" class="kfs-alert__icon" :size="20" />
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="kfs-alert kfs-alert--error">
                        <x-kfs.icon name="warning" class="kfs-alert__icon" :size="20" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
