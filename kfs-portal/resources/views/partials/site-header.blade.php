<header class="kfs-site-header">
    <div class="kfs-container kfs-site-header__inner">
        <x-kfs.brand />

        <nav class="kfs-site-nav" id="kfs-site-nav" aria-label="التنقل الرئيسي">
            <a href="{{ url('/#systems') }}">الأنظمة</a>
            <a href="{{ url('/#features') }}">المزايا</a>
            <a href="{{ url('/#how') }}">كيف تعمل</a>
            <a href="{{ url('/#contact') }}">تواصل</a>
        </nav>

        <div class="kfs-site-actions">
            @auth
                <a href="{{ route('portal.dashboard') }}" class="kfs-btn kfs-btn--gold kfs-btn--sm">
                    بوابة المستخدم
                    <x-kfs.icon name="arrow-left" :size="17" />
                </a>
            @else
                <a href="{{ route('login') }}" class="kfs-btn kfs-btn--ghost kfs-btn--sm">تسجيل الدخول</a>
                <a href="{{ route('portal.dashboard') }}" class="kfs-btn kfs-btn--sm">
                    الدخول الموحّد
                    <x-kfs.icon name="arrow-left" :size="17" />
                </a>
            @endauth

            <button type="button" class="kfs-burger" data-kfs-burger aria-expanded="false" aria-controls="kfs-site-nav" aria-label="فتح القائمة">
                <x-kfs.icon name="menu" :size="22" />
            </button>
        </div>
    </div>
</header>
