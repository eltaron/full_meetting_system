@extends('errors.layout')

@section('title', 'غير مصرّح')
@section('code', '٤٠٣')
@section('heading', 'لا تملك صلاحية الدخول')
@section('message', 'هذه الصفحة تتطلب صلاحية لا يملكها حسابك. تواصل مع مدير البوابة إذا كنت تعتقد أن هذا خطأ.')

@section('icon')
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="4.5" y="10.5" width="15" height="9" rx="1.5"/><path d="M8.25 10.5V7.5a3.75 3.75 0 0 1 7.5 0v3"/></svg>
@endsection

