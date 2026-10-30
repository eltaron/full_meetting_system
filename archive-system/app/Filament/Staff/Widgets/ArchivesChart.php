<?php

namespace App\Filament\Staff\Widgets;

use App\Models\Subject;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ArchivesChart extends ChartWidget
{
    protected static ?string $heading = 'معدل الأرشفة اليومي (آخر 7 أيام)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $counts = Subject::visibleTo(auth()->user())
            ->where('created_at', '>=', today()->subDays(6))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->select(DB::raw('count(*) as count'), DB::raw('DATE(created_at) as date'))
            ->pluck('count', 'date');

        // كل يوم يظهر حتى لو لم يتم أرشفة شيء فيه
        $days = collect(range(6, 0))->map(fn ($i) => today()->subDays($i));

        return [
            'datasets' => [
                [
                    'label' => 'المستندات المؤرشفة',
                    'data' => $days->map(fn ($d) => $counts[$d->toDateString()] ?? 0)->all(),
                    'fill' => 'start',
                    'borderColor' => '#0f3d6e',
                    'backgroundColor' => 'rgba(15, 61, 110, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->translatedFormat('l d/m'))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
