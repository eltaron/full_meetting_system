<?php

namespace App\Filament\Staff\Widgets;

use App\Models\Subject;
use Filament\Widgets\ChartWidget;

class ArchiveBySourceChart extends ChartWidget
{
    protected static ?string $heading = 'أكثر التصنيفات استخداماً';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ids = Subject::visibleTo(auth()->user())->where('is_active', true)->select('id');

        $stats = \App\Models\Tag::query()
            ->withCount(['subjects' => fn ($q) => $q->whereIn('subjects.id', $ids)])
            ->orderByDesc('subjects_count')
            ->take(6)
            ->get()
            ->where('subjects_count', '>', 0);

        return [
            'datasets' => [
                [
                    'label' => 'عدد المستندات',
                    'data' => $stats->pluck('subjects_count')->values()->all(),
                    'backgroundColor' => ['#0f3d6e', '#c9a227', '#10b981', '#ef4444', '#8b5cf6', '#64748b'],
                ],
            ],
            'labels' => $stats->pluck('name')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
