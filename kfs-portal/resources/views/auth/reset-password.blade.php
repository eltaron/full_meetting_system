@extends('layouts.auth')

@section('title', 'تعيين كلمة مرور جديدة')

@section('aside_title', 'كلمة مرور جديدة')
@section('aside_highlight', 'لحساب أكثر أماناً')
@section('aside_lead', 'اختر كلمة مرور قوية لا تقل عن ثمانية أحرف، ويفضّل أن تجمع بين حروف وأرقام ورموز.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">إعادة التعيين</span>
        <h1>تعيين كلمة المرور</h1>
        <p>أدخل بريدك وكلمة المرور الجديدة.</p>
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

    <form method="POST" action="{{ route('password.update') }}" class="kfs-auth-form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="kfs-field">
            <label class="kfs-label" for="email">البريد الإلكتروني</label>
            <input id="email" name="email" type="email" class="kfs-input @error('email') has-error @enderror"
                   value="{{ old('email', $request->email) }}" autocomplete="email" dir="ltr" required readonly>
            @error('email') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="password">كلمة المرور الجديدة</label>
            <input id="password" name="password" type="password" class="kfs-input @error('password') has-error @enderror"
                   placeholder="٨ أحرف على الأقل" autocomplete="new-password" required>
            @error('password') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="password_confirmation">تأكيد كلمة المرور</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="kfs-input"
                   placeholder="أعد إدخال كلمة المرور" autocomplete="new-password" required>
        </div>

        <button type="submit" class="kfs-btn kfs-btn--block kfs-btn--lg">
            حفظ كلمة المرور
            <x-kfs.icon name="lock" :size="19" />
        </button>
    </form>
@endsection
