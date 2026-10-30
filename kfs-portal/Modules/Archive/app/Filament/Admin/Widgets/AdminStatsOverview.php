<?php

namespace Modules\Archive\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            \Filament\Widgets\StatsOverviewWidget\Stat::make('إجمالي المستندات المؤرشفة', \Modules\Archive\Models\Subject::count())
                ->description('إجمالي الأرشيف الموحد في كل الإدارات')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('عدد الموظفين النشطين', \App\Models\User::count())
                ->description('إجمالي الموظفين المسجلين في المنظومة')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            \Filament\Widgets\StatsOverviewWidget\Stat::make('عدد الإدارات المترابطة', \Modules\Archive\Models\Department::count())
                ->description('الإدارات المسجلة بالديوان العام')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning'),
        ];
    }
}
