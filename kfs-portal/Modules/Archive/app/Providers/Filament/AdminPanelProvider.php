<?php

namespace Modules\Archive\Providers\Filament;

use App\Filament\Concerns\KfsPanelDefaults;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    use KfsPanelDefaults;

    public function panel(Panel $panel): Panel
    {
        return $this->applyKfsDefaults($panel, 'منظومة الأرشيف الإلكتروني', 'الإدارة العامة للأرشيف')
            ->id('archive')
            ->path('archive')
            ->discoverResources(
                in: module_path('Archive', 'app/Filament/Admin/Resources'),
                for: 'Modules\\Archive\\Filament\\Admin\\Resources',
            )
            ->discoverPages(
                in: module_path('Archive', 'app/Filament/Admin/Pages'),
                for: 'Modules\\Archive\\Filament\\Admin\\Pages',
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: module_path('Archive', 'app/Filament/Admin/Widgets'),
                for: 'Modules\\Archive\\Filament\\Admin\\Widgets',
            )
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make('الأرشيف'),
                NavigationGroup::make('التقارير'),
                NavigationGroup::make('الإعدادات والنظام'),
                NavigationGroup::make('المستخدمون والصلاحيات'),
            ]);
    }
}
