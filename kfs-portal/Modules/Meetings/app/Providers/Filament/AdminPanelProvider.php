<?php

namespace Modules\Meetings\Providers\Filament;

use App\Filament\Concerns\KfsPanelDefaults;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\MaxWidth;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class AdminPanelProvider extends PanelProvider
{
    use KfsPanelDefaults;

    public function panel(Panel $panel): Panel
    {
        return $this->applyKfsDefaults($panel, 'منظومة الاجتماعات', 'الأمانة العامة للمحافظة')
            ->id('meetings')
            ->path('meetings')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(
                in: module_path('Meetings', 'app/Filament/Resources'),
                for: 'Modules\\Meetings\\Filament\\Resources',
            )
            ->discoverPages(
                in: module_path('Meetings', 'app/Filament/Pages'),
                for: 'Modules\\Meetings\\Filament\\Pages',
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: module_path('Meetings', 'app/Filament/Widgets'),
                for: 'Modules\\Meetings\\Filament\\Widgets',
            )
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make('الاجتماعات'),
                NavigationGroup::make('الإعدادات'),
                NavigationGroup::make('المستخدمون والصلاحيات'),
            ])
            ->plugins([
                FilamentFullCalendarPlugin::make()
                    ->selectable()
                    ->editable(),
            ]);
    }
}
