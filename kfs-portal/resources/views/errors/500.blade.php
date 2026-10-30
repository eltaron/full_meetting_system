@extends('errors.layout')

@section('title', 'خطأ في الخادم')
@section('code', '٥٠٠')
@section('heading', 'حدث خطأ غير متوقع')
@section('message', 'تعذّر إتمام الطلب حالياً. تم تسجيل المشكلة، ويمكنك المحاولة مرة أخرى بعد قليل.')

@section('icon')
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4.5 21 19.5H3L12 4.5Z"/><path d="M12 9.75v4.5M12 17.25h.01"/></svg>
@endsection

