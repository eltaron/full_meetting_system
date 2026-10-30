@extends('errors.layout')

@section('title', 'انتهت الجلسة')
@section('code', '٤١٩')
@section('heading', 'انتهت صلاحية الجلسة')
@section('message', 'انتهت صلاحية الصفحة لأسباب أمنية. أعد تحميل الصفحة ثم حاول مرة أخرى.')

@section('icon')
    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.25"/><path d="M12 7.5v4.5l3 1.75"/></svg>
@endsection

