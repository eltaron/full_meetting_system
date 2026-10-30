<?php

namespace Modules\Archive\Filament\Admin\Widgets;

use Modules\Archive\Models\Subject;
use Modules\Archive\Models\Department;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DepartmentsDistributionChart extends ChartWidget
{
    // تعريب العنوان
    protected static ?string $heading = 'توزيع الوثائق مؤرشفة حسب الإدارة';

    protected function getData(): array
    {
        // جلب أكثر 5 إدارات لديهم ملفات مؤرشفة لضمان نظافة الرسم
        $data = Subject::query()
            ->select('department_id', DB::raw('count(*) as count'))
            ->groupBy('department_id')
            ->with('department')
            ->orderBy('count', 'desc')
            ->take(6) // زيادة لـ 6 لبيان التنافس
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الوثائق',
                    'data' => $data->pluck('count')->toArray(),
                    // مصفوفة ألوان متناغمة (أزرق، ذهبي، أخضر، أحمر، رمادي، أصفر)
                    'backgroundColor' => [
                        '#1e3a8a', // الكحلي الرسمي
                        '#fbbf24', // الذهبي
                        '#10b981', // أخضر نجاح
                        '#f43f5e', // أحمر تحذيري
                        '#64748b', // رمادي هادئ
                        '#f97316'  // برتقالي
                    ],
                    'hoverOffset' => 10 // حركة تفاعلية عند الماوس
                ],
            ],
            // إذا كان اسم الإدارة مفقود يعرض 'إدارة عامة'
            'labels' => $data->map(fn($item) => $item->department?->name ?? 'غير محدد')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // يمكنك استخدام doughnut لشكل أكثر عصرية
    }

    // لتصغير الرسم في المساحة الجانبية
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom', // جعل الأسماء تحت الدائرة لسهولة القراءة بالعربي
                ],
            ],
        ];
    }
}
