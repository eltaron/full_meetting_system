<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TagResource\Pages;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// إدارة التصنيفات (Tags) المستخدمة لتصنيف المستندات والبحث بها
class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'التصنيفات';
    protected static ?string $navigationGroup = 'الأرشيف';
    protected static ?string $modelLabel = 'تصنيف';
    protected static ?string $pluralModelLabel = 'التصنيفات';
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم التصنيف')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('التصنيف')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('عدد المستندات')
                    ->counts('subjects')
                    ->sortable(),
            ])
            ->defaultSort('subjects_count', 'desc')
            ->actions([
                Tables\Actions\Action::make('documents')
                    ->label('المستندات')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->url(fn (Tag $record) => SubjectResource::getUrl('index', [
                        'tableFilters' => ['tags' => ['values' => [$record->id]], 'is_active' => ['value' => '']],
                    ])),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف')
                    ->modalDescription('سيُزال التصنيف من كل المستندات المرتبطة به (المستندات نفسها لن تُحذف).'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTags::route('/'),
        ];
    }
}
