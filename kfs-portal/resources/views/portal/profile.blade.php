@extends('layouts.portal')

@section('title', 'ملفي الشخصي')

@php($user = auth()->user())

@section('content')
    <div class="kfs-welcome">
        <div>
            <span class="kfs-eyebrow">الحساب</span>
            <h1>ملفي الشخصي</h1>
            <p>حدّث بياناتك الأساسية وكلمة المرور. تُدار الأنظمة المتاحة من إدارة البوابة.</p>
        </div>
        <a href="{{ route('portal.dashboard') }}" class="kfs-btn kfs-btn--ghost kfs-btn--sm">
            <x-kfs.icon name="arrow-right" :size="17" />
            العودة للبوابة
        </a>
    </div>

    <div class="kfs-profile-grid">
        <div class="kfs-stack">
            {{-- البيانات الأساسية --}}
            <div class="kfs-card kfs-card--pad">
                <div class="kfs-row" style="justify-content:space-between; margin-bottom:18px;">
                    <h2 style="font-size:17px; font-weight:800; color:var(--kfs-green-900);">البيانات الأساسية</h2>
                    <span class="kfs-badge">معلومات الحساب</span>
                </div>

                @if ($errors->any())
                    <div class="kfs-alert kfs-alert--error" style="margin-bottom:18px;">
                        <x-kfs.icon name="warning" class="kfs-alert__icon" :size="20" />
                        <div class="kfs-stack" style="gap:4px;">
                            @foreach ($errors->all() as $error)
                                <span>{{ $error }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('portal.profile.update') }}" class="kfs-stack">
                    @csrf
                    @method('PUT')

                    <div class="kfs-form-grid">
                        <div class="kfs-field">
                            <label class="kfs-label" for="name">الاسم الكامل</label>
                            <input id="name" name="name" type="text" class="kfs-input" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="kfs-field">
                            <label class="kfs-label" for="email">البريد الإلكتروني</label>
                            <input id="email" name="email" type="email" class="kfs-input" value="{{ old('email', $user->email) }}" dir="ltr" required>
                        </div>

                        <div class="kfs-field">
                            <label class="kfs-label" for="phone_number">رقم الهاتف</label>
                            <input id="phone_number" name="phone_number" type="tel" class="kfs-input" value="{{ old('phone_number', $user->phone_number ?? $user->phone) }}" dir="ltr">
                        </div>

                        <div class="kfs-field">
                            <label class="kfs-label" for="job_title">المسمى الوظيفي</label>
                            <input id="job_title" name="job_title" type="text" class="kfs-input" value="{{ old('job_title', $user->job_title) }}">
                        </div>

                        <div class="kfs-field kfs-field--full">
                            <label class="kfs-label" for="job_grade">الدرجة الوظيفية</label>
                            <input id="job_grade" name="job_grade" type="text" class="kfs-input" value="{{ old('job_grade', $user->job_grade) }}" placeholder="مثال: الدرجة الثانية">
                        </div>
                    </div>

                    <div class="kfs-form-actions">
                        <button type="submit" class="kfs-btn">
                            حفظ التعديلات
                            <x-kfs.icon name="check" :size="18" />
                        </button>
                    </div>
                </form>
            </div>

            {{-- كلمة المرور --}}
            <div class="kfs-card kfs-card--pad">
                <h2 style="font-size:17px; font-weight:800; color:var(--kfs-green-900); margin-bottom:6px;">تغيير كلمة المرور</h2>
                <p class="kfs-small kfs-text-muted" style="margin-bottom:18px;">استخدم كلمة مرور قوية لا تقل عن ثمانية أحرف.</p>

                <form method="POST" action="{{ route('portal.profile.password') }}" class="kfs-stack">
                    @csrf
                    @method('PUT')

                    <div class="kfs-field">
                        <label class="kfs-label" for="current_password">كلمة المرور الحالية</label>
                        <input id="current_password" name="current_password" type="password" class="kfs-input" autocomplete="current-password" required>
                    </div>

                    <div class="kfs-form-grid">
                        <div class="kfs-field">
                            <label class="kfs-label" for="new_password">كلمة المرور الجديدة</label>
                            <input id="new_password" name="password" type="password" class="kfs-input" autocomplete="new-password" required>
                        </div>
                        <div class="kfs-field">
                            <label class="kfs-label" for="new_password_confirmation">تأكيد كلمة المرور</label>
                            <input id="new_password_confirmation" name="password_confirmation" type="password" class="kfs-input" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div class="kfs-form-actions">
                        <button type="submit" class="kfs-btn kfs-btn--ghost">
                            <x-kfs.icon name="lock" :size="18" />
                            تحديث كلمة المرور
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- بطاقة الهوية --}}
        <aside class="kfs-card kfs-card--pad">
            <div style="display:flex; flex-direction:column; align-items:center; text-align:center; gap:10px; padding-bottom:18px; border-bottom:1px solid var(--kfs-line);">
                <span class="kfs-avatar" style="width:72px; height:72px; flex-basis:72px; font-size:26px;">{{ mb_substr($user->name, 0, 1) }}</span>
                <div>
                    <strong style="display:block; font-size:17px; color:var(--kfs-green-900);">{{ $user->name }}</strong>
                    <span class="kfs-small kfs-text-muted">{{ $user->job_title ?: 'مستخدم المنظومة' }}</span>
                </div>
                <span class="kfs-badge {{ $user->is_active ? '' : 'kfs-badge--red' }}">
                    {{ $user->is_active ? 'حساب نشط' : 'حساب موقوف' }}
                </span>
            </div>

            <div class="kfs-meta-list kfs-mt-3">
                <div class="kfs-meta-row"><span>اسم المستخدم</span><strong dir="ltr">{{ $user->username ?? '—' }}</strong></div>
                <div class="kfs-meta-row"><span>الدور الإداري</span><strong>{{ $user->roles->first()?->name ? str_replace('_', ' ', $user->roles->first()->name) : ($user->role ?? '—') }}</strong></div>
                <div class="kfs-meta-row"><span>الإدارة</span><strong>{{ $user->department?->name ?? '—' }}</strong></div>
                <div class="kfs-meta-row"><span>المدير المباشر</span><strong>{{ $user->manager?->name ?? '—' }}</strong></div>
                <div class="kfs-meta-row"><span>الأنظمة المتاحة</span><strong>{{ count($user->accessibleSystems()) }}</strong></div>
                <div class="kfs-meta-row"><span>آخر دخول</span><strong>{{ $user->last_login_at?->format('Y-m-d H:i') ?? '—' }}</strong></div>
            </div>

            <div class="kfs-auth-note kfs-mt-4">
                <x-kfs.icon name="shield" :size="18" />
                <span>لتعديل الأنظمة المتاحة أو الدور الإداري، تواصل مع مدير البوابة.</span>
            </div>
        </aside>
    </div>
@endsection
