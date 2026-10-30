<footer class="kfs-site-footer" id="contact">
    <div class="kfs-container">
        <div class="kfs-footer-grid">
            <div class="kfs-footer-col">
                <x-kfs.brand light />
                <p class="kfs-footer-about">
                    بوابة إلكترونية موحدة تجمع الأرشيف الإلكتروني، وإدارة الاجتماعات، ومتابعة التكليفات
                    في منظومة واحدة تحت مظلة محافظة كفر الشيخ، بهوية رسمية وبنية صلاحيات دقيقة.
                </p>
            </div>

            <div class="kfs-footer-col">
                <h5>الأنظمة</h5>
                <ul>
                    <li><a href="{{ url('/#systems') }}">الأرشيف الإلكتروني</a></li>
                    <li><a href="{{ url('/#systems') }}">إدارة الاجتماعات</a></li>
                    <li><a href="{{ url('/#systems') }}">التكليفات والمتابعة</a></li>
                    <li><a href="{{ route('login') }}">الدخول الموحّد</a></li>
                </ul>
            </div>

            <div class="kfs-footer-col">
                <h5>روابط سريعة</h5>
                <ul>
                    <li><a href="{{ url('/#features') }}">المزايا</a></li>
                    <li><a href="{{ url('/#how') }}">كيف تعمل المنظومة</a></li>
                    <li><a href="{{ route('register') }}">طلب حساب</a></li>
                    <li><a href="{{ route('password.request') }}">استعادة كلمة المرور</a></li>
                </ul>
            </div>

            <div class="kfs-footer-col">
                <h5>تواصل معنا</h5>
                <ul>
                    <li><a href="mailto:info@kfs.gov.eg">info@kfs.gov.eg</a></li>
                    <li><a href="tel:+20473320000" dir="ltr">+20 47 332 0000</a></li>
                    <li><span>ديوان عام المحافظة — كفر الشيخ</span></li>
                    <li><span>الأحد إلى الخميس، ٩ ص — ٣ م</span></li>
                </ul>
            </div>
        </div>

        <div class="kfs-footer-bottom">
            <span>© {{ date('Y') }} محافظة كفر الشيخ — جميع الحقوق محفوظة.</span>
            <span>المنظومة الإدارية الموحدة · الإصدار ١.٠</span>
        </div>
    </div>
</footer>
