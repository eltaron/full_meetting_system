<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Subject;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ArchiveTrendChart extends ChartWidget
{
    // 1. تعريب عنوان الرسم
    protected static ?string $heading = 'معدل الإنتاجية الشهري للأرشفة (العام الحالي)';

    // تحديد مكان ظهورها في الصفحة
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // جلب البيانات من الداتا بيز (عدد الملفات لكل شهر في السنة الحالية)
        $results = Subject::select(
            DB::raw('COUNT(*) as count'),
            DB::raw('MONTH(created_at) as month')
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // تجهيز مصفوفة لـ 12 شهر (حتى لو شهر مفيهوش داتا يظهر 0)
        $monthlyCounts = [];
        $monthNames = [
            1 => 'يناير',
            2 => 'فبراير',
            3 => 'مارس',
            4 => 'أبريل',
            5 => 'مايو',
            6 => 'يونيو',
            7 => 'يوليو',
            8 => 'أغسطس',
            9 => 'سبتمبر',
            10 => 'أكتوبر',
            11 => 'نوفمبر',
            12 => 'ديسمبر'
        ];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyCounts[] = $results[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'إجمالي المعاملات مؤرشفة',
                    'data' => $monthlyCounts,
                    'borderColor' => '#1e3a8a', // الأزرق الملكي للمحافظة
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(30, 58, 138, 0.1)',
                    'tension' => 0.4, // انسيابية الخط
                ],
            ],
            'labels' => array_values($monthNames),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // رسم بياني خطي للاتجاه
    }
}
