<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1F6E51">
    <title>@yield('title', 'بوابة المستخدم') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/kfs-royal.css') }}">
    @stack('styles')
</head>
<body class="kfs">
    @php($user = auth()->user())

    <div class="kfs-portal">
        <header class="kfs-portal-header">
            <div class="kfs-container kfs-portal-header__inner">
                <x-kfs.brand />

                <nav class="kfs-portal-nav" aria-label="تنقل البوابة">
                    <a href="{{ route('portal.dashboard') }}" class="{{ request()->routeIs('portal.dashboard') ? 'is-active' : '' }}">الرئيسية</a>
                    <a href="{{ route('portal.profile') }}" class="{{ request()->routeIs('portal.profile') ? 'is-active' : '' }}">ملفي الشخصي</a>
                </nav>

                <div class="kfs-row">
                    <a href="{{ route('portal.profile') }}" class="kfs-user-chip" style="text-decoration:none;">
                        <span class="kfs-avatar">{{ mb_substr($user->name, 0, 1) }}</span>
                        <span class="kfs-user-chip__meta">
                            <strong>{{ $user->name }}</strong>
                            <span>{{ $user->job_title ?: 'مستخدم المنظومة' }}</span>
                        </span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="kfs-btn kfs-btn--ghost kfs-btn--sm" title="تسجيل الخروج">
                            <x-kfs.icon name="logout" :size="17" />
                            خروج
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="kfs-portal-main">
            <div class="kfs-container">
                @if (session('success'))
                    <div class="kfs-alert kfs-alert--success kfs-mt-2" style="margin-bottom:22px;">
                        <x-kfs.icon name="check-circle" class="kfs-alert__icon" :size="20" />
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="kfs-alert kfs-alert--error kfs-mt-2" style="margin-bottom:22px;">
                        <x-kfs.icon name="warning" class="kfs-alert__icon" :size="20" />
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <footer style="border-top:1px solid var(--kfs-line); background:var(--kfs-card);">
            <div class="kfs-container" style="padding-block:18px; display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; font-size:12.5px; color:var(--kfs-muted);">
                <span>© {{ date('Y') }} محافظة كفر الشيخ — المنظومة الإدارية الموحدة</span>
                <span>دخول موحّد · صلاحيات حسب التصريح</span>
            </div>
        </footer>
    </div>
</body>
</html>
