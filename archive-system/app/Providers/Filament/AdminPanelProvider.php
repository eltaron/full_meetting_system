<?php

namespace App\Providers\Filament;

use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;

// لوحة مدير المنظومة
class AdminPanelProvider extends PanelProvider
{
    use ConfiguresArchivePanel;

    public function panel(Panel $panel): Panel
    {
        return $this->applyArchiveDefaults($panel)
            ->id('admin')
            ->path('admin')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make('الأرشيف'),
                NavigationGroup::make('الرقابة والمتابعة'),
                NavigationGroup::make('الهيكل الإداري'),
                NavigationGroup::make('التحليل والتقارير'),
            ]);
    }
}
