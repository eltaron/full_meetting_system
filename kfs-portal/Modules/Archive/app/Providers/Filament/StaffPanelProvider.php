<?php

namespace Modules\Archive\Providers\Filament;

use App\Filament\Concerns\KfsPanelDefaults;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;

class StaffPanelProvider extends PanelProvider
{
    use KfsPanelDefaults;

    public function panel(Panel $panel): Panel
    {
        return $this->applyKfsDefaults($panel, 'أرشيفي — مساحة العمل', 'الإدارة العامة للأرشيف')
            ->id('archive-staff')
            ->path('archive/workspace')
            ->topNavigation()
            ->discoverResources(
                in: module_path('Archive', 'app/Filament/Staff/Resources'),
                for: 'Modules\\Archive\\Filament\\Staff\\Resources',
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: module_path('Archive', 'app/Filament/Staff/Widgets'),
                for: 'Modules\\Archive\\Filament\\Staff\\Widgets',
            )
            ->widgets([]);
    }
}
