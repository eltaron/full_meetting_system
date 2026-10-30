@extends('layouts.portal')

@section('title', 'بوابة المستخدم')

@php
    $user = auth()->user();
    $systems = $user->accessibleSystems();

    $meta = [
        'archive' => [
            'desc' => 'إدارة الوثائق والمراسلات ودورة حياة المستند مع التصنيف والمرفقات.',
            'caps' => ['الموضوعات والمرفقات', 'دورة حياة المستند', 'التقارير العامة'],
            'icon' => 'archive',
            'entry' => '/archive',
        ],
        'meetings' => [
            'desc' => 'جدولة الاجتماعات وإدارة القاعات والحضور ومتابعة القرارات.',
            'caps' => ['تقويم الاجتماعات', 'القاعات والحضور', 'القرارات والتنبيهات'],
            'icon' => 'calendar',
            'entry' => '/meetings',
        ],
        'followup' => [
            'desc' => 'إسناد التكليفات ومتابعة التنفيذ وتوقيعات الاعتماد.',
            'caps' => ['التكليفات والإسناد', 'لوحة المتابعة', 'التوقيعات والاعتماد'],
            'icon' => 'tasks',
            'entry' => '/followup/dashboard',
        ],
    ];
@endphp

@section('content')
    <div class="kfs-welcome">
        <div>
            <span class="kfs-eyebrow">بوابة المستخدم</span>
            <h1>أهلاً، {{ $user->name }}</h1>
            <p>اختر النظام الذي تريد الدخول إليه. تظهر هنا الأنظمة المصرّح لك بها فقط.</p>
        </div>

        @if ($user->last_login_at)
            <span class="kfs-badge kfs-badge--gold">
                <x-kfs.icon name="clock" :size="14" />
                آخر دخول: {{ $user->last_login_at->diffForHumans() }}
            </span>
        @endif
    </div>

    <div class="kfs-stat-grid">
        <div class="kfs-stat">
            <span class="kfs-stat__icon"><x-kfs.icon name="layers" :size="22" /></span>
            <span class="kfs-stat__body">
                <strong>{{ count($systems) }}</strong>
                <span>أنظمة متاحة لحسابك</span>
            </span>
        </div>

        <div class="kfs-stat">
            <span class="kfs-stat__icon"><x-kfs.icon name="shield" :size="22" /></span>
            <span class="kfs-stat__body">
                <strong>{{ $user->isSuperAdmin() ? 'مدير البوابة' : ($user->roles->first()?->name ? str_replace('_', ' ', $user->roles->first()->name) : 'مستخدم') }}</strong>
                <span>الدور الإداري</span>
            </span>
        </div>

        <div class="kfs-stat">
            <span class="kfs-stat__icon"><x-kfs.icon name="building" :size="22" /></span>
            <span class="kfs-stat__body">
                <strong>{{ $user->department?->name ?? '—' }}</strong>
                <span>الإدارة التابع لها</span>
            </span>
        </div>

        <div class="kfs-stat">
            <span class="kfs-stat__icon"><x-kfs.icon name="check-circle" :size="22" /></span>
            <span class="kfs-stat__body">
                <strong>{{ $user->is_active ? 'نشط' : 'موقوف' }}</strong>
                <span>حالة الحساب</span>
            </span>
        </div>
    </div>

    @if (empty($systems))
        <div class="kfs-access-banner">
            <x-kfs.icon name="info" :size="22" />
            <div>
                <strong>لم تُمنح صلاحية الدخول لأي نظام بعد.</strong>
                <div>تواصل مع مدير البوابة لتفعيل الأنظمة المناسبة لمهامك الوظيفية. يمكنك تحديث بياناتك من
                    <a href="{{ route('portal.profile') }}">ملفك الشخصي</a>.
                </div>
            </div>
        </div>
    @else
        <div class="kfs-launcher-grid">
            @foreach ($systems as $key => $system)
                @php($info = $meta[$key])
                <article class="kfs-launcher">
                    <div class="kfs-launcher__top">
                        <span class="kfs-launcher__icon"><x-kfs.icon :name="$info['icon']" :size="26" /></span>
                        <span class="kfs-badge">مُصرّح</span>
                    </div>

                    <div>
                        <h3>{{ $system['label'] }}</h3>
                        <p class="kfs-mt-2">{{ $info['desc'] }}</p>
                    </div>

                    <ul class="kfs-system-card__list">
                        @foreach ($info['caps'] as $cap)
                            <li><x-kfs.icon name="check" :size="16" /> {{ $cap }}</li>
                        @endforeach
                    </ul>

                    <div class="kfs-launcher__foot">
                        <a href="{{ url($info['entry']) }}" class="kfs-btn kfs-btn--block">
                            دخول النظام
                            <x-kfs.icon name="arrow-left" :size="18" />
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <div class="kfs-card kfs-card--pad kfs-mt-5" style="display:flex; align-items:center; justify-content:space-between; gap:18px; flex-wrap:wrap;">
        <div>
            <h3 style="font-size:16px; font-weight:800; color:var(--kfs-green-900);">هل تحتاج صلاحية إضافية؟</h3>
            <p class="kfs-small kfs-text-muted" style="margin-top:4px;">تُمنح صلاحيات الأنظمة من إدارة البوابة وفق طبيعة العمل.</p>
        </div>
        <a href="{{ route('portal.profile') }}" class="kfs-btn kfs-btn--ghost kfs-btn--sm">
            <x-kfs.icon name="user" :size="17" />
            بياناتي
        </a>
    </div>
@endsection
