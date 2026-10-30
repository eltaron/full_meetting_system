@extends('layouts.auth')

@section('title', 'الحساب قيد المراجعة')

@section('aside_title', 'طلبك في أيدٍ أمينة')
@section('aside_highlight', 'قيد المراجعة')
@section('aside_lead', 'يتولى مدير البوابة مراجعة طلبات الحساب وتحديد الأنظمة المناسبة لكل مستخدم قبل التفعيل.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">تم استلام الطلب</span>
        <h1>حسابك قيد المراجعة</h1>
        <p>
            شكراً لك{{ session('registered_name') ? '، ' . session('registered_name') : '' }}.
            تم إنشاء الحساب وبانتظار تفعيل مدير البوابة وتحديد الأنظمة المسموح لك بها.
        </p>
    </div>

    <div class="kfs-auth-note">
        <x-kfs.icon name="clock" :size="18" />
        <span>سيتمكن حسابك من الدخول بعد التفعيل. يمكنك التواصل مع إدارة البوابة لتسريع المراجعة.</span>
    </div>

    <a href="{{ route('login') }}" class="kfs-btn kfs-btn--block">
        العودة إلى تسجيل الدخول
        <x-kfs.icon name="arrow-left" :size="18" />
    </a>

    <a href="{{ url('/') }}" class="kfs-btn kfs-btn--ghost kfs-btn--block">
        الصفحة الرئيسية
    </a>
@endsection
