<?php

namespace Modules\Archive\Filament\Staff\Resources;

use Modules\Archive\Filament\Staff\Resources\TeamResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// موظفو الإدارة وإنتاجيتهم: لمدير الإدارة فقط (عرض بدون تعديل)
class TeamResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $slug = 'team';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'موظفو الإدارة';
    protected static ?string $modelLabel = 'موظف';
    protected static ?string $pluralModelLabel = 'موظفو الإدارة';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isManager() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('department')
            ->whereIn('department_id', auth()->user()->accessibleDepartmentIds())
            ->withCount([
                'subjects',
                'subjects as month_subjects_count' => fn (Builder $q) => $q
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month),
                'subjects as inactive_subjects_count' => fn (Builder $q) => $q->where('is_active', false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الموظف')
                    ->description(fn (User $record) => $record->job_title)
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('القسم')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('role')
                    ->label('الصفة')
                    ->badge(),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('إجمالي ما أرشفه')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('month_subjects_count')
                    ->label('هذا الشهر')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\TextColumn::make('inactive_subjects_count')
                    ->label('مستندات ملغاة')
                    ->sortable()
                    ->alignCenter()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->since()
                    ->placeholder('لم يدخل بعد')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('الحساب مفعل')
                    ->boolean(),
            ])
            ->defaultSort('month_subjects_count', 'desc')
            ->actions([
                Action::make('activity')
                    ->label('سجل النشاط')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(fn (User $record) => ActivityLogResource::getUrl('index', [
                        'tableFilters' => ['user_id' => ['value' => $record->id]],
                    ])),

                Action::make('documents')
                    ->label('مستنداته')
                    ->icon('heroicon-o-archive-box')
                    ->color('gray')
                    ->url(fn (User $record) => SubjectResource::getUrl('index', [
                        'tableFilters' => ['user_id' => ['value' => $record->id], 'is_active' => ['value' => '']],
                    ])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeam::route('/'),
        ];
    }
}
