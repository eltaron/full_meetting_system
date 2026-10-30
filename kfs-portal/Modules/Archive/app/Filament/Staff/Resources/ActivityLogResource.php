<?php

namespace Modules\Archive\Filament\Staff\Resources;

use Modules\Archive\Filament\Shared\ActivityLogTable;
use Modules\Archive\Filament\Shared\SubjectSchema;
use Modules\Archive\Filament\Staff\Resources\ActivityLogResource\Pages;
use Modules\Archive\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// متابعة نشاط موظفي الإدارة: لمدير الإدارة فقط
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'متابعة النشاط';
    protected static ?string $modelLabel = 'حركة';
    protected static ?string $pluralModelLabel = 'سجل نشاط موظفي الإدارة';
    protected static ?int $navigationSort = 3;

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
            ->with(['user', 'department'])
            ->whereIn('department_id', auth()->user()->accessibleDepartmentIds());
    }

    public static function table(Table $table): Table
    {
        return ActivityLogTable::make(
            $table,
            SubjectSchema::usersOf(auth()->user()->accessibleDepartmentIds()),
            subjectUrl: fn (ActivityLog $record) => $record->subject_id && ! $record->subject?->trashed()
                ? SubjectResource::getUrl('view', ['record' => $record->subject_id])
                : null,
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
