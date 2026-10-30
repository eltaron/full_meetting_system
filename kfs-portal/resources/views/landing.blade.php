@extends('layouts.public')

@section('title', 'بوابة محافظة كفر الشيخ — المنظومة الإدارية الموحدة')

@section('content')

    {{-- ===================== الواجهة الرئيسية ===================== --}}
    <section class="kfs-hero">
        <div class="kfs-container kfs-hero__inner">
            <div>
                <span class="kfs-hero__eyebrow">محافظة كفر الشيخ · البوابة الإلكترونية الموحدة</span>

                <h1>
                    منظومة واحدة تدير
                    <em>الأرشيف والاجتماعات والتكليفات</em>
                    بكفاءة
                </h1>

                <p class="kfs-hero__lead">
                    بوابة موحّدة تربط الأنظمة الإدارية الثلاثة في تجربة دخول واحدة:
                    أرشيف إلكتروني محكم، اجتماعات وقرارات متابعة، وتكليفات بمسارات اعتماد واضحة —
                    بهوية بصرية واحدة وصلاحيات دقيقة لكل مستخدم.
                </p>

                <div class="kfs-hero__actions">
                    @auth
                        <a href="{{ route('portal.dashboard') }}" class="kfs-btn kfs-btn--gold kfs-btn--lg">
                            الدخول إلى بوابتي
                            <x-kfs.icon name="arrow-left" :size="19" />
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="kfs-btn kfs-btn--gold kfs-btn--lg">
                            الدخول الموحّد
                            <x-kfs.icon name="arrow-left" :size="19" />
                        </a>
                        <a href="{{ route('register') }}" class="kfs-btn kfs-btn--light kfs-btn--lg">
                            طلب حساب جديد
                        </a>
                    @endauth
                </div>

                <div class="kfs-hero__meta">
                    <div class="kfs-hero__meta-item">
                        <strong>٣</strong>
                        <span>أنظمة مترابطة</span>
                    </div>
                    <div class="kfs-hero__meta-item">
                        <strong>دخول</strong>
                        <span>موحّد وآمن</span>
                    </div>
                    <div class="kfs-hero__meta-item">
                        <strong>صلاحيات</strong>
                        <span>دقيقة لكل نظام</span>
                    </div>
                    <div class="kfs-hero__meta-item">
                        <strong>٢٤/٧</strong>
                        <span>إتاحة للخدمة</span>
                    </div>
                </div>
            </div>

            <div class="kfs-systems-mini">
                <div class="kfs-systems-mini__head">
                    <strong>الأنظمة المتاحة</strong>
                    <span>دخول حسب التصريح</span>
                </div>

                <a href="{{ auth()->check() ? route('portal.dashboard') : route('login') }}" class="kfs-mini-row">
                    <span class="kfs-mini-row__icon"><x-kfs.icon name="archive" :size="22" /></span>
                    <span class="kfs-mini-row__body">
                        <strong>الأرشيف الإلكتروني</strong>
                        <span>تصنيف وربط الوثائق ودورة حياة المستند</span>
                    </span>
                    <x-kfs.icon name="arrow-up-left" class="kfs-mini-row__arrow" :size="18" />
                </a>

                <a href="{{ auth()->check() ? route('portal.dashboard') : route('login') }}" class="kfs-mini-row">
                    <span class="kfs-mini-row__icon"><x-kfs.icon name="calendar" :size="22" /></span>
                    <span class="kfs-mini-row__body">
                        <strong>إدارة الاجتماعات</strong>
                        <span>جدولة الاجتماعات ومتابعة القرارات</span>
                    </span>
                    <x-kfs.icon name="arrow-up-left" class="kfs-mini-row__arrow" :size="18" />
                </a>

                <a href="{{ auth()->check() ? route('portal.dashboard') : route('login') }}" class="kfs-mini-row">
                    <span class="kfs-mini-row__icon"><x-kfs.icon name="tasks" :size="22" /></span>
                    <span class="kfs-mini-row__body">
                        <strong>التكليفات والمتابعة</strong>
                        <span>إسناد المهام وتوقيعات الاعتماد</span>
                    </span>
                    <x-kfs.icon name="arrow-up-left" class="kfs-mini-row__arrow" :size="18" />
                </a>

                <div style="padding: 2px 6px 0; font-size: 11.5px; color: #B9CCC4;">
                    الدخول إلى أي نظام يتطلب تصريحاً مسبقاً من مدير البوابة.
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== شريط الثقة ===================== --}}
    <div class="kfs-strip">
        <div class="kfs-container kfs-strip__inner">
            <div class="kfs-strip__item"><x-kfs.icon name="shield" :size="19" /> صلاحيات مبنية على الأدوار</div>
            <div class="kfs-strip__item"><x-kfs.icon name="lock" :size="19" /> جلسة موحّدة محمية</div>
            <div class="kfs-strip__item"><x-kfs.icon name="layers" :size="19" /> بيانات مترابطة بين الأنظمة</div>
            <div class="kfs-strip__item"><x-kfs.icon name="clock" :size="19" /> متاح طوال أيام العمل</div>
        </div>
    </div>

    {{-- ===================== الأنظمة ===================== --}}
    <section class="kfs-section" id="systems">
        <div class="kfs-container">
            <div class="kfs-section-head">
                <div class="kfs-section-head__text">
                    <span class="kfs-eyebrow">ثلاث منظومات · بوابة واحدة</span>
                    <h2 class="kfs-section-title">كل ما تحتاجه الإدارة في مكان واحد</h2>
                    <p class="kfs-section-lead">
                        لم تعد كل إدارة بحاجة إلى نظام منفصل ودخول مستقل. البوابة تجمع الأنظمة الثلاثة
                        خلف حساب واحد، ويظهر لكل مستخدم ما صُرّح له به فقط.
                    </p>
                </div>
                <a href="{{ route('login') }}" class="kfs-btn kfs-btn--ghost">
                    دخول المنظومة
                    <x-kfs.icon name="arrow-left" :size="18" />
                </a>
            </div>

            <div class="kfs-system-grid">
                <article class="kfs-system-card">
                    <span class="kfs-system-card__icon"><x-kfs.icon name="archive" :size="28" /></span>
                    <div>
                        <h3>الأرشيف الإلكتروني</h3>
                        <p>إدارة الوثائق والمراسلات الرسمية بدورة حياة كاملة من التسجيل إلى الإتلاف، مع تصنيف هرمي وربط بين
                            المستندات.</p>
                    </div>
                    <ul class="kfs-system-card__list">
                        <li><x-kfs.icon name="check" :size="16" /> تصنيف الإدارات والموضوعات والوسوم</li>
                        <li><x-kfs.icon name="check" :size="16" /> دورة حياة المستند والتقارير العامة</li>
                        <li><x-kfs.icon name="check" :size="16" /> مرفقات ومخطط زمني لكل موضوع</li>
                    </ul>
                    <div class="kfs-system-card__foot">
                        <span class="kfs-badge">لوحة إدارية + مساحة عمل</span>
                        <x-kfs.icon name="arrow-left" class="kfs-text-muted" :size="20" />
                    </div>
                </article>

                <article class="kfs-system-card">
                    <span class="kfs-system-card__icon"><x-kfs.icon name="calendar" :size="28" /></span>
                    <div>
                        <h3>إدارة الاجتماعات</h3>
                        <p>جدولة الاجتماعات، وإدارة القاعات والحضور، وتوثيق القرارات ومتابعة تنفيذها من شاشة واحدة.</p>
                    </div>
                    <ul class="kfs-system-card__list">
                        <li><x-kfs.icon name="check" :size="16" /> تقويم اجتماعات وقاعات ومرفقات</li>
                        <li><x-kfs.icon name="check" :size="16" /> دعوات الحضور ومحاضر الاجتماعات</li>
                        <li><x-kfs.icon name="check" :size="16" /> متابعة القرارات والتنبيهات</li>
                    </ul>
                    <div class="kfs-system-card__foot">
                        <span class="kfs-badge">تقويم تفاعلي</span>
                        <x-kfs.icon name="arrow-left" class="kfs-text-muted" :size="20" />
                    </div>
                </article>

                <article class="kfs-system-card">
                    <span class="kfs-system-card__icon"><x-kfs.icon name="tasks" :size="28" /></span>
                    <div>
                        <h3>التكليفات والمتابعة</h3>
                        <p>إسناد التكليفات ومتابعة تنفيذها عبر تسلسل إداري واضح، مع توقيعات الاعتماد وسجل حالة لكل مهمة.</p>
                    </div>
                    <ul class="kfs-system-card__list">
                        <li><x-kfs.icon name="check" :size="16" /> تكليفات ومسارات اعتماد وتوقيعات</li>
                        <li><x-kfs.icon name="check" :size="16" /> لوحة متابعة وتقارير حالة</li>
                        <li><x-kfs.icon name="check" :size="16" /> جهات ومصادر التكليفات</li>
                    </ul>
                    <div class="kfs-system-card__foot">
                        <span class="kfs-badge">متابعة لحظية</span>
                        <x-kfs.icon name="arrow-left" class="kfs-text-muted" :size="20" />
                    </div>
                </article>
            </div>
        </div>
    </section>

    {{-- ===================== المزايا ===================== --}}
    <section class="kfs-section kfs-section--paper" id="features">
        <div class="kfs-container">
            <div class="kfs-section-head">
                <div class="kfs-section-head__text">
                    <span class="kfs-eyebrow">لماذا بوابة موحّدة؟</span>
                    <h2 class="kfs-section-title">دقة إدارية وهوية رسمية واحدة</h2>
                    <p class="kfs-section-lead">صُممت البوابة لتناسب طبيعة العمل الحكومي: واضحة، هادئة، ومحكومة بصلاحيات.
                    </p>
                </div>
            </div>

            <div class="kfs-feature-grid">
                <div class="kfs-feature">
                    <span class="kfs-feature__icon"><x-kfs.icon name="shield" :size="22" /></span>
                    <h4>صلاحية لكل نظام</h4>
                    <p>يمكن منح المستخدم حق الدخول لنظام واحد أو أكثر، ويُمنع الوصول إلى ما لم يُصرّح له به.</p>
                </div>
                <div class="kfs-feature">
                    <span class="kfs-feature__icon"><x-kfs.icon name="user" :size="22" /></span>
                    <h4>حساب واحد</h4>
                    <p>بيانات المستخدم وإدارته ومرؤوسوه في مكان واحد، وتنعكس في الأنظمة الثلاثة تلقائياً.</p>
                </div>
                <div class="kfs-feature">
                    <span class="kfs-feature__icon"><x-kfs.icon name="layers" :size="22" /></span>
                    <h4>بيانات مترابطة</h4>
                    <p>الإدارات والتصنيفات والجهات مشتركة، فلا تكرار لإدخال البيانات بين الأنظمة.</p>
                </div>
                <div class="kfs-feature">
                    <span class="kfs-feature__icon"><x-kfs.icon name="chart" :size="22" /></span>
                    <h4>متابعة وقرار</h4>
                    <p>لوحات مؤشرات وتقارير تساعد متخذ القرار على رؤية الوضع الفعلي بسرعة.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== كيف تعمل ===================== --}}
    <section class="kfs-section" id="how">
        <div class="kfs-container">
            <div class="kfs-section-head">
                <div class="kfs-section-head__text">
                    <span class="kfs-eyebrow">خطوات واضحة</span>
                    <h2 class="kfs-section-title">من طلب الحساب إلى الدخول</h2>
                    <p class="kfs-section-lead">مسار مضبوط يضمن أن كل مستخدم يصل إلى الأنظمة المصرّح له بها فقط.</p>
                </div>
            </div>

            <div class="kfs-steps">
                <div class="kfs-step">
                    <h4>طلب الحساب</h4>
                    <p>يسجّل المستخدم بياناته الوظيفية عبر نموذج طلب الحساب.</p>
                </div>
                <div class="kfs-step">
                    <h4>مراجعة وتفعيل</h4>
                    <p>يراجع مدير البوابة الطلب ويحدد الأنظمة المسموح بها.</p>
                </div>
                <div class="kfs-step">
                    <h4>دخول موحّد</h4>
                    <p>يدخل المستخدم مرة واحدة ويجد الأنظمة المتاحة له في بوابته.</p>
                </div>
                <div class="kfs-step">
                    <h4>عمل يومي</h4>
                    <p>يتنقل بين الأرشيف والاجتماعات والتكليفات دون تسجيل دخول جديد.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== نداء الختام ===================== --}}
    <section class="kfs-section kfs-section--tight">
        <div class="kfs-container">
            <div class="kfs-cta">
                <div class="kfs-cta__text">
                    <h2>ابدأ من بوابتك الموحّدة</h2>
                    <p>سجّل الدخول للوصول إلى الأنظمة المصرّح لك بها، أو اطلب حساباً جديداً للانضمام إلى المنظومة.</p>
                </div>
                <div class="kfs-cta__actions">
                    <a href="{{ route('login') }}" class="kfs-btn kfs-btn--gold kfs-btn--lg">
                        تسجيل الدخول
                        <x-kfs.icon name="arrow-left" :size="19" />
                    </a>
                    <a href="{{ route('register') }}" class="kfs-btn kfs-btn--light kfs-btn--lg">طلب حساب</a>
                </div>
            </div>
        </div>
    </section>

@endsection
