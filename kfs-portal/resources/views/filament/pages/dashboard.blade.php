<x-filament-panels::page>
    <div class=\"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6\">
        <x-filament::section>
            <div class=\"flex items-center gap-4\">
                <div class=\"p-3 bg-primary-50 dark:bg-primary-500/10 rounded-lg\">
                    <x-heroicon-o-archive-box class=\"w-8 h-8 text-primary-600\" />
                </div>
                <div class=\"flex-1\">
                    <h3 class=\"text-lg font-semibold\">نظام الأرشيف</h3>
                    <p class=\"text-sm text-gray-500\">إدارة المكاتبات والمراسلات</p>
                </div>
            </div>
            <div class=\"mt-4\">
                <x-filament::button tag=\"a\" href=\"/admin\" color=\"primary\">الدخول للنظام</x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class=\"flex items-center gap-4\">
                <div class=\"p-3 bg-primary-50 dark:bg-primary-500/10 rounded-lg\">
                    <x-heroicon-o-calendar-days class=\"w-8 h-8 text-primary-600\" />
                </div>
                <div class=\"flex-1\">
                    <h3 class=\"text-lg font-semibold\">نظام الاجتماعات</h3>
                    <p class=\"text-sm text-gray-500\">إدارة الاجتماعات والجداول</p>
                </div>
            </div>
            <div class=\"mt-4\">
                <x-filament::button tag=\"a\" href=\"/meetings\" color=\"primary\">الدخول للنظام</x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class=\"flex items-center gap-4\">
                <div class=\"p-3 bg-primary-50 dark:bg-primary-500/10 rounded-lg\">
                    <x-heroicon-o-clipboard-document-check class=\"w-8 h-8 text-primary-600\" />
                </div>
                <div class=\"flex-1\">
                    <h3 class=\"text-lg font-semibold\">نظام متابعة التكليفات</h3>
                    <p class=\"text-sm text-gray-500\">متابعة وتنفيذ التكليفات</p>
                </div>
            </div>
            <div class=\"mt-4\">
                <x-filament::button tag=\"a\" href=\"/followup\" color=\"primary\">الدخول للنظام</x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
