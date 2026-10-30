@extends('layouts.auth')

@section('title', 'تسجيل الدخول')

@section('aside_title', 'مرحباً بعودتك إلى')
@section('aside_highlight', 'بوابة المحافظة')
@section('aside_lead', 'أدخل بيانات حسابك للوصول إلى الأنظمة التي صُرّح لك بها. تُدار الصلاحيات مركزياً من إدارة البوابة.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">الدخول الموحّد</span>
        <h1>تسجيل الدخول</h1>
        <p>استخدم البريد الإلكتروني أو اسم المستخدم وكلمة المرور.</p>
    </div>

    @if ($errors->any())
        <div class="kfs-alert kfs-alert--error">
            <x-kfs.icon name="warning" class="kfs-alert__icon" :size="20" />
            <div class="kfs-stack" style="gap:4px;">
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="kfs-auth-form" novalidate>
        @csrf

        <div class="kfs-field">
            <label class="kfs-label" for="login">البريد الإلكتروني أو اسم المستخدم</label>
            <input
                id="login"
                name="login"
                type="text"
                class="kfs-input @error('login') has-error @enderror"
                value="{{ old('login') }}"
                placeholder="name@kfs.gov.eg أو اسم المستخدم"
                autocomplete="username"
                autofocus
                required>
            @error('login')
                <span class="kfs-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="password">كلمة المرور</label>
            <input
                id="password"
                name="password"
                type="password"
                class="kfs-input @error('password') has-error @enderror"
                placeholder="••••••••"
                autocomplete="current-password"
                required>
            @error('password')
                <span class="kfs-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="kfs-auth-row">
            <label class="kfs-check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span>إبقاء الجلسة مفتوحة</span>
            </label>

            <a href="{{ route('password.request') }}" class="kfs-small" style="font-weight:700;">نسيت كلمة المرور؟</a>
        </div>

        <button type="submit" class="kfs-btn kfs-btn--block kfs-btn--lg">
            دخول إلى البوابة
            <x-kfs.icon name="arrow-left" :size="19" />
        </button>
    </form>

    <div class="kfs-auth-divider">أو</div>

    <p class="kfs-auth-alt">
        لا تملك حساباً؟ <a href="{{ route('register') }}">اطلب حساباً جديداً</a>
    </p>

    <div class="kfs-auth-note">
        <x-kfs.icon name="info" :size="18" />
        <span>الحسابات الجديدة تُفعّل بعد مراجعة مدير البوابة وتحديد الأنظمة المسموح بها.</span>
    </div>
@endsection
