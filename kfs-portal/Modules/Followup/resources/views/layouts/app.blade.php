<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'التكليفات والمتابعة') — {{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/css/style.css', 'resources/js/app.js'])

    {{-- Unified "KFS Royal Egyptian" palette: re-points the compiled navy/orange
         utilities to Egyptian green + gold and forces the light theme. --}}
    <link rel="stylesheet" href="{{ asset('css/kfs-followup.css') }}">

    @stack('styles')
</head>

<body class="bg-bg-soft text-slate-800 font-cairo">
    <script>
        (function() {
            // The portal is light-only by design; ignore any stored dark preference.
            document.documentElement.setAttribute('data-theme', 'light');
            try { localStorage.setItem('theme', 'light'); } catch (e) {}
        })();
    </script>

    <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

    @include('partials.sidebar')

    <div class="lg:mr-72 min-h-screen flex flex-col transition-all duration-300">

        @include('partials.header')

        <main class="flex-1 p-6 sm:p-10">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-bold flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl font-bold flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- مكان محتوى كل صفحة --}}
            @yield('content')
        </main>

        <footer class="p-6 text-center text-slate-400 text-sm border-t border-slate-200 bg-white/50">
            <p>منظومة التكليفات والمتابعة &copy; {{ date('Y') }} — محافظة كفر الشيخ</p>
        </footer>
    </div>

    @include('partials.logout-modal')

    {{-- سكريبت مشترك لكل الصفحات: فتح/قفل السايدبار، الدروب داون، المودال --}}
    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <script>
        (function() {
            // Theme toggle is intentionally removed — the portal is light-only.
            // Keep the user dropdown click-out behaviour.
            document.addEventListener('click', function(e) {
                var ddBtn = document.getElementById('user-dropdown-btn');
                var ddMenu = document.getElementById('user-dropdown');
                if (ddMenu && ddBtn && !ddBtn.contains(e.target) && !ddMenu.contains(e.target)) {
                    ddMenu.classList.add('hidden');
                }
            });
        })();
    </script>

    {{-- أي سكريبت خاص بصفحة معينة يتحط هنا --}}
    @stack('scripts')
</body>

</html>