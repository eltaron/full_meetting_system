<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Pages\DocumentLifecycle;
use App\Filament\Admin\Resources\SubjectResource\Pages;
use App\Filament\Shared\SubjectSchema;
use App\Models\Subject;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationLabel = 'الأرشيف العام الموحد';
    protected static ?string $navigationGroup = 'الأرشيف';
    protected static ?string $modelLabel = 'مستند';
    protected static ?string $pluralModelLabel = 'سجلات الأرشيف العام';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getGloballySearchableAttributes(): array
    {
        return ['archive_number', 'title', 'description', 'reference_number', 'tags.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return ['رقم القيد' => $record->archive_number, 'الإدارة' => $record->department?->name];
    }

    // مدير المنظومة يرى المحذوف أيضاً (عبر فلتر المحذوفات)
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema(SubjectSchema::form(forAdmin: true));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(SubjectSchema::infolist(withTimeline: true));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(SubjectSchema::columns(showDepartment: true))
            ->defaultSort('created_at', 'desc')
            ->filters(
                [
                    ...SubjectSchema::filters(
                        showDepartment: true,
                        usersOptions: fn () => User::orderBy('name')->pluck('name', 'id')->all(),
                    ),
                    Tables\Filters\TrashedFilter::make()->label('المحذوفات'),
                ],
                layout: Tables\Enums\FiltersLayout::AboveContentCollapsible,
            )
            ->filtersFormColumns(4)
            ->actions([
                SubjectSchema::attachmentsAction(),
                Tables\Actions\ViewAction::make()->label('تفاصيل'),
                Tables\Actions\ActionGroup::make([
                    Action::make('lifecycle')
                        ->label('دورة عمل المستند')
                        ->icon('heroicon-o-clock')
                        ->url(fn (Subject $record) => DocumentLifecycle::getUrl(['subject' => $record->id])),
                    Tables\Actions\EditAction::make()->label('تعديل'),
                    SubjectSchema::deactivateAction(),
                    SubjectSchema::activateAction(),
                    Tables\Actions\DeleteAction::make()->label('حذف')
                        ->modalDescription('سيُنقل المستند إلى المحذوفات ويمكن استرجاعه لاحقاً.'),
                    Tables\Actions\RestoreAction::make()->label('استرجاع'),
                    Tables\Actions\ForceDeleteAction::make()->label('حذف نهائي')
                        ->modalDescription('سيتم حذف المستند ومرفقاته نهائياً ولا يمكن التراجع. سيبقى أثر العملية في سجل الحركات.'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (Subject $record) => static::getUrl('view', ['record' => $record]))
            ->searchPlaceholder('ابحث في كامل أرشيف المحافظة برقم القيد، العنوان، الجهة أو التصنيف...');
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
