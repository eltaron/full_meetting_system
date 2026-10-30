<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Filament\Admin\Widgets\AdminStatsOverview; // سننشئ هذه المربعات
use App\Filament\Admin\Widgets\ArchiveTrendChart; // سننشئ هذا الرسم البياني
use App\Filament\Admin\Widgets\DepartmentsDistributionChart; // سننشئ هذا الرسم الدائري

class GeneralReports extends Page
{
    // تعريب القائمة والأيقونة
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'التقارير والإحصائيات العامة';
    protected static ?string $title = 'لوحة التقارير المركزية لمحافظة كفر الشيخ';

    // وضعها في مجموعة مستقلة في القائمة الجانبية (اختياري)
    protected static ?string $navigationGroup = 'التحليل والتقارير';

    protected static string $view = 'filament.admin.pages.general-reports';

    // تحديد الـ Widgets التي تظهر في هذه الصفحة حصراً
    protected function getHeaderWidgets(): array
    {
        return [
            AdminStatsOverview::class,
            ArchiveTrendChart::class,
            DepartmentsDistributionChart::class,
        ];
    }

    // جعل الصفحة تأخذ عرض الشاشة بالكامل للرسوم البيانية
    public function getHeaderWidgetsColumns(): int | array
    {
        return 2; // عرض الرسوم البيانية في عمودين بجانب بعض
    }
}
