<?php

namespace Modules\Archive\Filament\Admin\Resources;

use Modules\Archive\Filament\Admin\Pages\DocumentLifecycle;
use Modules\Archive\Filament\Admin\Resources\ActivityLogResource\Pages;
use Modules\Archive\Filament\Shared\ActivityLogTable;
use Modules\Archive\Models\ActivityLog;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// سجل الحركات الكامل لكل المنظومة (للقراءة فقط - لا يمكن تعديله أو حذفه)
class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'سجل الحركات';
    protected static ?string $navigationGroup = 'الرقابة والمتابعة';
    protected static ?string $modelLabel = 'حركة';
    protected static ?string $pluralModelLabel = 'سجل الحركات';

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

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'department']);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogTable::make(
            $table,
            fn () => User::orderBy('name')->pluck('name', 'id')->all(),
            showDepartmentFilter: true,
            subjectUrl: fn (ActivityLog $record) => $record->subject_id
                ? DocumentLifecycle::getUrl(['subject' => $record->subject_id])
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
