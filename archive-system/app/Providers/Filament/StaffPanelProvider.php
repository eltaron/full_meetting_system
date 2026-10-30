<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;

// لوحة الموظفين ومديري الإدارات
class StaffPanelProvider extends PanelProvider
{
    use ConfiguresArchivePanel;

    public function panel(Panel $panel): Panel
    {
        return $this->applyArchiveDefaults($panel)
            ->id('staff')
            ->path('staff')
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Staff/Resources'), for: 'App\\Filament\\Staff\\Resources')
            ->discoverPages(in: app_path('Filament/Staff/Pages'), for: 'App\\Filament\\Staff\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Staff/Widgets'), for: 'App\\Filament\\Staff\\Widgets')
            ->widgets([]);
    }
}
