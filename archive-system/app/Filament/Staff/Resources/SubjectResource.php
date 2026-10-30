<?php

namespace App\Filament\Staff\Resources;

use App\Filament\Shared\SubjectSchema;
use App\Filament\Staff\Resources\SubjectResource\Pages;
use App\Models\Subject;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'الأرشيف الإلكتروني';
    protected static ?string $modelLabel = 'مستند';
    protected static ?string $pluralModelLabel = 'أرشيف الإدارة';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['archive_number', 'title', 'description', 'reference_number', 'tags.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['رقم القيد' => $record->archive_number, 'الاتجاه' => $record->direction?->getLabel()];
    }

    // الموظف يرى أرشيف إدارته فقط، ومدير الإدارة يرى إدارته والأقسام التابعة لها
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->visibleTo(auth()->user());
    }

    public static function form(Form $form): Form
    {
        return $form->schema(SubjectSchema::form());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(SubjectSchema::infolist(withTimeline: auth()->user()->isManager()));
    }

    public static function table(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->columns(SubjectSchema::columns(showDepartment: $user->isManager()))
            ->defaultSort('created_at', 'desc')
            ->filters(
                SubjectSchema::filters(
                    usersOptions: $user->isManager() ? SubjectSchema::usersOf($user->accessibleDepartmentIds()) : null,
                ),
                layout: Tables\Enums\FiltersLayout::AboveContentCollapsible,
            )
            ->filtersFormColumns(4)
            ->actions([
                SubjectSchema::attachmentsAction(),
                Tables\Actions\ViewAction::make()->label('تفاصيل'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\ActionGroup::make([
                    SubjectSchema::deactivateAction(),
                    SubjectSchema::activateAction(),
                ]),
                // لا يوجد حذف للموظفين: الحذف من صلاحيات مدير المنظومة فقط
            ])
            ->recordUrl(fn (Subject $record) => static::getUrl('view', ['record' => $record]))
            ->searchPlaceholder('ابحث برقم القيد، العنوان، رقم الوارد/الصادر، الجهة أو التصنيف...')
            ->emptyStateHeading('لا توجد مستندات تطابق بحثك');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'view' => Pages\ViewSubject::route('/{record}'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
