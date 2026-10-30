<?php

namespace App\Filament\Shared;

use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

// جدول سجل الحركات المشترك (مدير المنظومة + مدير الإدارة)
class ActivityLogTable
{
    public static function make(Table $table, \Closure $usersOptions, bool $showDepartmentFilter = false, ?\Closure $subjectUrl = null): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('الوقت')
                    ->dateTime('Y-m-d h:i A')
                    ->description(fn (ActivityLog $record) => $record->created_at->diffForHumans())
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('الموظف')
                    ->description(fn (ActivityLog $record) => $record->department?->name)
                    ->placeholder('مستخدم محذوف')
                    ->searchable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('الحركة')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ActivityLog::ACTIONS[$state] ?? $state)
                    ->color(fn (string $state) => ActivityLog::ACTION_COLORS[$state] ?? 'gray'),

                Tables\Columns\TextColumn::make('description')
                    ->label('المستند')
                    ->placeholder('-')
                    ->searchable()
                    ->wrap()
                    ->url($subjectUrl),

                Tables\Columns\TextColumn::make('properties')
                    ->label('تفاصيل')
                    ->state(fn (ActivityLog $record) => self::details($record))
                    ->placeholder('-')
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters(array_values(array_filter([
                SelectFilter::make('user_id')
                    ->label('الموظف')
                    ->options($usersOptions)
                    ->searchable(),

                SelectFilter::make('action')
                    ->label('نوع الحركة')
                    ->options(ActivityLog::ACTIONS)
                    ->multiple(),

                $showDepartmentFilter
                    ? SelectFilter::make('department_id')
                        ->label('الإدارة')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                    : null,

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                        Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['from'], fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'], fn ($q, $d) => $q->whereDate('created_at', '<=', $d))),
            ])), layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(4)
            ->actions([])
            ->bulkActions([])
            ->paginated([25, 50, 100]);
    }

    public static function details(ActivityLog $record): ?string
    {
        $p = $record->properties ?? [];

        return match ($record->action) {
            'deactivated' => isset($p['reason']) ? 'السبب: ' . $p['reason'] : null,
            'viewed_attachment' => $p['file'] ?? null,
            'updated' => isset($p['changes'])
                ? 'عدّل: ' . collect(array_keys($p['changes']))->map(fn ($f) => ActivityLog::FIELD_LABELS[$f] ?? $f)->join('، ')
                : null,
            default => null,
        };
    }
}
