<?php

namespace App\Filament\Admin\Resources;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'إدارة الموظفين';
    protected static ?string $navigationGroup = 'الهيكل الإداري';
    protected static ?string $modelLabel = 'موظف';
    protected static ?string $pluralModelLabel = 'الموظفين';

    // تفعيل البحث العام (Global Search)
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'username', 'email'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الموظف')
                    ->description('تأكد من تعيين الموظف للإدارة الصحيحة ليتمكن من رؤية أرشيفها')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم الموظف')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('job_title')
                            ->label('المسمى الوظيفي')
                            ->placeholder('مثال: أخصائي أرشيف، رئيس قسم...')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('username')
                            ->label('اسم المستخدم (للدخول)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->revealable()
                            ->rule(Password::min(8)->letters()->numbers())
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->helperText(fn (string $context) => $context === 'edit' ? 'اتركها فارغة للإبقاء على كلمة المرور الحالية' : '8 أحرف على الأقل وتحتوي على حروف وأرقام')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('الصلاحيات')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->label('الصفة / الصلاحية')
                            ->options(UserRole::class)
                            ->default(UserRole::Employee->value)
                            ->native(false)
                            ->live()
                            ->required()
                            // منع مدير المنظومة من سحب صلاحياته من نفسه بالخطأ
                            ->disabled(fn (?User $record) => $record?->is(auth()->user()))
                            ->helperText('مدير الإدارة يرى أرشيف إدارته والأقسام التابعة لها ويتابع نشاط موظفيها'),

                        Forms\Components\Select::make('department_id')
                            ->label('الإدارة التابع لها')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('role') !== UserRole::Admin->value)
                            ->visible(fn (Get $get) => $get('role') !== UserRole::Admin->value),

                        Forms\Components\Toggle::make('is_active')
                            ->label('الحساب مفعل')
                            ->helperText('أوقف الحساب عند نقل الموظف أو خروجه للمعاش بدلاً من حذفه')
                            ->default(true)
                            ->disabled(fn (?User $record) => $record?->is(auth()->user())),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->description(fn (User $record) => $record->job_title)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('username')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('role')
                    ->label('الصفة')
                    ->badge(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('الإدارة')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('subjects_count')
                    ->label('ما أرشفه')
                    ->counts('subjects')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->since()
                    ->placeholder('لم يدخل بعد')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('مفعل')
                    ->disabled(fn (User $record) => $record->is(auth()->user())),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('الصفة')
                    ->options(UserRole::class),

                SelectFilter::make('department_id')
                    ->label('تصفية بالإدارة')
                    ->relationship('department', 'name')
                    ->searchable(),

                TernaryFilter::make('is_active')
                    ->label('حالة الحساب')
                    ->trueLabel('المفعلة')
                    ->falseLabel('الموقوفة'),
            ])
            ->actions([
                Action::make('activity')
                    ->label('النشاط')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('gray')
                    ->url(fn (User $record) => ActivityLogResource::getUrl('index', [
                        'tableFilters' => ['user_id' => ['value' => $record->id]],
                    ])),
                Action::make('print_account')
                    ->label('طباعة الحساب')
                    ->icon('heroicon-o-printer')
                    ->color('warning')
                    // الربط بالصفحة الخارجية وفتحها في تبويب جديد
                    ->url(fn (User $record) => route('user.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                // الموظف الذي له مستندات لا يُحذف (حفاظاً على الأرشيف)، يتم إيقاف حسابه فقط
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record) => $record->is(auth()->user()) || $record->subjects()->withTrashed()->exists()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
