<?php

namespace Modules\Archive\Filament\Admin\Resources;

use Modules\Archive\Filament\Admin\Resources\DepartmentResource\Pages;
use Modules\Archive\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    // 1. تعريب الواجهة والأيقونة
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'الهيكل الإداري';
    protected static ?string $modelLabel = 'إدارة';
    protected static ?string $pluralModelLabel = 'الأقسام والإدارات';
    protected static ?string $navigationGroup = 'الهيكل الإداري';

    // تفعيل البحث العام (Global Search) برأس اسم الإدارة
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الإدارة / القسم')
                    ->description('قم بإضافة القسم وتحديد تبعيته الإدارية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الإدارة / القسم')
                            ->required()
                            ->placeholder('مثلاً: الإدارة المالية، الشؤون الإدارية...')
                            ->maxLength(255),

                        // ربط الإدارة بالقسم الرئيسي (Hierarchy)
                        Forms\Components\Select::make('parent_id')
                            ->label('تتبع إدارة رئيسية')
                            // علاقة الـ parent، مع استبعاد الإدارة نفسها والأقسام التابعة لها لمنع التبعية الدائرية
                            ->relationship('parent', 'name', fn (Builder $query, ?Department $record) => $query
                                ->when($record, fn ($q) => $q->whereNotIn('id', $record->withDescendantIds())))
                            ->searchable()
                            ->preload()
                            ->placeholder('اختر الإدارة الرئيسية (اختياري)')
                            ->hint('اتركها فارغة إذا كانت هذه هي الإدارة العليا (الأم)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الإدارة')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // عرض الإدارة الرئيسية التابع لها القسم بشكل أنيق
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('تابعة لـ')
                    ->badge()
                    ->color('info')
                    ->placeholder('إدارة رئيسية كبرى')
                    ->searchable(),

                // إظهار عدد الموظفين المنضمين لهذه الإدارة
                Tables\Columns\TextColumn::make('users_count')
                    ->label('عدد الموظفين')
                    ->counts('users')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('عدد المستندات')
                    ->counts('subjects')
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // فلتر لعرض أقسام إدارة رئيسية محددة
                SelectFilter::make('parent_id')
                    ->label('تصفية بالإدارة الرئيسية')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                // لا تُحذف إدارة عليها موظفين أو مستندات أو أقسام تابعة (حماية الأرشيف)
                Tables\Actions\DeleteAction::make()->label('حذف')
                    ->hidden(fn (Department $record) => ! $record->isDeletable()),
            ])
            ->searchPlaceholder('ابحث باسم الإدارة...');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
