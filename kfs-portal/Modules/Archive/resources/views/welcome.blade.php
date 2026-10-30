<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>منظومة الأرشفة الموحدة - محافظة كفر الشيخ</title>
    <!-- استدعاء خطوط جوجل (خط Cairo ممتاز للهويات الحكومية) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .gold-border {
            border-bottom: 4px solid #D4AF37;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <!-- الحاوية الرئيسية -->
    <div class="max-w-4xl w-full text-center space-y-8 animate-fade-in-down">

        <!-- جزء اللوجو -->
        <div class="relative inline-block group">
            <div class="relative  p-4 ">
                <img src="{{ asset('logo.png') }}" alt="لوجو كفر الشيخ" class="h-40 w-auto mx-auto">
            </div>
        </div>

        <!-- النصوص -->
        <div class="space-y-4">
            <h1 class="text-4xl md:text-5xl font-black text-slate-800 tracking-tight leading-tight">
                محافظة <span style="color: #D4AF37">كفر الشيخ</span>
                <br>
                <span class="text-3xl md:text-4xl font-bold text-slate-600 mt-2 block ">منظومة الأرشفة الإلكترونية
                    الموحدة</span>
            </h1>

            <div class="flex justify-center">
                <div class="w-24 h-1 bg-[#D4AF37] rounded-full"></div>
            </div>

            <p class="text-lg text-slate-500 max-w-2xl mx-auto font-medium">
                بوابة التحول الرقمي لإدارة وحفظ الوثائق الحكومية بشكل آمن ومنظم،
                لتعزيز كفاءة العمل الإداري وسرعة استرجاع البيانات.
            </p>
        </div>

        <!-- الأزرار -->
        <div class="flex flex-col md:flex-row gap-4 justify-center items-center mt-10">
            @auth
                <a href="/staff"
                    class="px-10 py-4 bg-[#D4AF37] text-white font-bold rounded-xl shadow-[0_4px_0_rgb(180,140,50)] hover:shadow-none hover:translate-y-1 transition-all duration-150 flex items-center gap-2">
                    <span>الانتقال لوحة التحكم</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <a href="/staff/login"
                    class="px-10 py-4 bg-[#D4AF37] text-white font-bold rounded-xl shadow-[0_4px_0_rgb(180,140,50)] hover:shadow-none hover:translate-y-1 transition-all duration-150 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    تسجيل الدخول للمنظومة
                </a>
            @endauth
        </div>

        <!-- التذييل -->
        <footer class="mt-20 pt-8 border-t border-slate-200 text-slate-400 text-sm">
            <p>جميع الحقوق محفوظة &copy; {{ date('Y') }} - لإدارة العامة للتحول الرقمي بمحافظة كفر الشيخ</p>
        </footer>
    </div>

</body>

</html>
