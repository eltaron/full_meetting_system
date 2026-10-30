<?php

namespace App\Filament\Staff\Widgets;

use App\Enums\DocumentDirection;
use App\Models\Subject;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubjectStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $visible = fn () => Subject::visibleTo($user)->where('is_active', true);

        $stats = [
            Stat::make($user->isManager() ? 'إجمالي أرشيف الإدارة' : 'إجمالي أرشيف القسم', $visible()->count())
                ->description('المستندات السارية')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('الوارد / الصادر هذا الشهر',
                $visible()->where('direction', DocumentDirection::Incoming)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count()
                . ' / ' .
                $visible()->where('direction', DocumentDirection::Outgoing)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->count())
                ->description('عدد المستندات المؤرشفة في ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrows-right-left')
                ->color('info'),

            Stat::make('أرشفتُه بنفسي', Subject::where('user_id', $user->id)->count())
                ->description('المستندات التي قمت بتسجيلها شخصياً')
                ->descriptionIcon('heroicon-m-user')
                ->color('success'),
        ];

        if ($user->isManager()) {
            $ids = $user->accessibleDepartmentIds();

            $stats[] = Stat::make('موظفو الإدارة', User::whereIn('department_id', $ids)->where('is_active', true)->count())
                ->description('دخل منهم اليوم: ' . User::whereIn('department_id', $ids)->whereDate('last_login_at', today())->count())
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning');

            $stats[] = Stat::make('مستندات ملغاة', Subject::visibleTo($user)->where('is_active', false)->count())
                ->description('تحتاج مراجعة مدير الإدارة')
                ->descriptionIcon('heroicon-m-no-symbol')
                ->color('danger');
        }

        return $stats;
    }
}
