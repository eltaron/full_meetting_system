@extends('layouts.auth')

@section('title', 'طلب حساب جديد')

@section('aside_title', 'انضم إلى')
@section('aside_highlight', 'المنظومة الإدارية الموحدة')
@section('aside_lead', 'أنشئ حسابك الوظيفي، وسيتولى مدير البوابة مراجعته وتحديد الأنظمة التي يمكنك الدخول إليها.')

@section('content')
    <div class="kfs-auth-card__head">
        <span class="kfs-eyebrow">حساب جديد</span>
        <h1>طلب حساب</h1>
        <p>أدخل بياناتك الوظيفية بدقة لتسريع المراجعة.</p>
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

    <form method="POST" action="{{ route('register.store') }}" class="kfs-auth-form" novalidate>
        @csrf

        <div class="kfs-field">
            <label class="kfs-label" for="name">الاسم الكامل <span class="kfs-req">*</span></label>
            <input id="name" name="name" type="text" class="kfs-input @error('name') has-error @enderror"
                   value="{{ old('name') }}" placeholder="الاسم كما هو في السجلات الرسمية" autocomplete="name" required>
            @error('name') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="email">البريد الإلكتروني <span class="kfs-req">*</span></label>
            <input id="email" name="email" type="email" class="kfs-input @error('email') has-error @enderror"
                   value="{{ old('email') }}" placeholder="name@kfs.gov.eg" autocomplete="email" dir="ltr" required>
            @error('email') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="job_title">المسمى الوظيفي</label>
            <input id="job_title" name="job_title" type="text" class="kfs-input @error('job_title') has-error @enderror"
                   value="{{ old('job_title') }}" placeholder="مثال: أخصائي أرشيف">
            @error('job_title') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="phone_number">رقم الهاتف</label>
            <input id="phone_number" name="phone_number" type="tel" class="kfs-input @error('phone_number') has-error @enderror"
                   value="{{ old('phone_number') }}" placeholder="01xxxxxxxxx" dir="ltr">
            @error('phone_number') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="password">كلمة المرور <span class="kfs-req">*</span></label>
            <input id="password" name="password" type="password" class="kfs-input @error('password') has-error @enderror"
                   placeholder="٨ أحرف على الأقل" autocomplete="new-password" required>
            @error('password') <span class="kfs-error">{{ $message }}</span> @enderror
        </div>

        <div class="kfs-field">
            <label class="kfs-label" for="password_confirmation">تأكيد كلمة المرور <span class="kfs-req">*</span></label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="kfs-input"
                   placeholder="أعد إدخال كلمة المرور" autocomplete="new-password" required>
        </div>

        <button type="submit" class="kfs-btn kfs-btn--block kfs-btn--lg">
            إرسال طلب الحساب
            <x-kfs.icon name="arrow-left" :size="19" />
        </button>
    </form>

    <div class="kfs-auth-divider">لديك حساب؟</div>

    <p class="kfs-auth-alt">
        <a href="{{ route('login') }}">العودة إلى تسجيل الدخول</a>
    </p>

    <div class="kfs-auth-note">
        <x-kfs.icon name="info" :size="18" />
        <span>لن يمنحك إنشاء الحساب أي صلاحية تلقائياً. يتم تفعيل الحساب وتحديد الأنظمة المتاحة بعد المراجعة.</span>
    </div>
@endsection
