@extends('layouts.auth')

@section('title', 'استعادة كلمة المرور')

@section('aside_title', 'استعادة الوصول إلى')
@section('aside_highlight', 'حسابك')
@section('aside_lead', 'أدخل بريدك الإلكتروني المسجّل وسنرسل إليك رابطاً آمناً لإعادة تعيين كلمة المرور.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">استعادة كلمة المرور</span>
        <h1>نسيت كلمة المرور؟</h1>
        <p>أدخل البريد الإلكتروني المرتبط بحسابك.</p>
    </div>

    @if ($errors->any())
        <div class="kfs-alert kfs-alert--error">
            <x-kfs.icon name="warning" class="kfs-alert__icon" :size="20" />
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="kfs-auth-form" novalidate>
        @csrf

        <div class="kfs-field">
            <label class="kfs-label" for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" class="kfs-input @error('email') has-error @enderror"
                   value="{{ old('email') }}" placeholder="name@kfs.gov.eg" autocomplete="email" dir="ltr" required autofocus>
            @error('email') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="kfs-btn kfs-btn--block kfs-btn--lg">
            إرسال رابط الاستعادة
            <x-kfs.icon name="mail" :size="19" />
        </button>
    </form>

    <div class="kfs-auth-divider">تذكّرت كلمة المرور؟</div>

    <p class="kfs-auth-alt">
        <a href="{{ route('login') }}">العودة إلى تسجيل الدخول</a>
    </p>
@endsection
