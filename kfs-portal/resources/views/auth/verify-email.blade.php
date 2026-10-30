@extends('layouts.auth')

@section('title', 'تفعيل البريد الإلكتروني')

@section('aside_title', 'خطوة أخيرة')
@section('aside_highlight', 'لتفعيل بريدك')
@section('aside_lead', 'أرسلنا رابط تحقق إلى بريدك الإلكتروني. تأكيد البريد يساعد على استعادة الحساب وتأمينه.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">التحقق من البريد</span>
        <h1>فعّل بريدك الإلكتروني</h1>
        <p>اضغط على الرابط المرسل إلى <strong dir="ltr">{{ auth()->user()?->email ?? session('email', 'بريدك') }}</strong></p>
    </div>

    @if (session('status') === 'verification-link-sent')
        <div class="kfs-alert kfs-alert--success">
            <x-kfs.icon name="check-circle" class="kfs-alert__icon" :size="20" />
            <span>تم إرسال رابط تحقق جديد إلى بريدك الإلكتروني.</span>
        </div>
    @endif

    <div class="kfs-alert kfs-alert--gold">
        <x-kfs.icon name="mail" class="kfs-alert__icon" :size="20" />
        <span>لم يصل الرابط؟ تحقق من مجلد الرسائل غير المرغوبة، أو أعد إرساله.</span>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="kfs-auth-form">
        @csrf
        <button type="submit" class="kfs-btn kfs-btn--block">
            إعادة إرسال رابط التحقق
            <x-kfs.icon name="mail" :size="18" />
        </button>
    </form>

    <div class="kfs-auth-divider">أو</div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="kfs-btn kfs-btn--ghost kfs-btn--block">تسجيل الخروج</button>
    </form>
@endsection
