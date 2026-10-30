<?php

namespace Modules\Archive\Filament\Shared;

use Modules\Archive\Enums\Confidentiality;
use Modules\Archive\Enums\DocumentDirection;
use Modules\Archive\Enums\DocumentType;
use Modules\Archive\Models\ActivityLog;
use Modules\Archive\Models\Department;
use Modules\Archive\Models\Subject;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * نموذج وجدول المستندات المشترك بين لوحة الإدارة ولوحة الموظفين
 */
class SubjectSchema
{
    public const ACCEPTED_FILE_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/tiff',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public const MONTHS = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
        7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];

    public static function form(bool $forAdmin = false): array
    {
        return [
            Forms\Components\Section::make('بيانات القيد')
                ->icon('heroicon-o-identification')
                ->description('رقم القيد يُولّد تلقائياً عند الحفظ')
                ->schema([
                    Forms\Components\Placeholder::make('archive_number_display')
                        ->label('رقم القيد الأرشيفي')
                        ->content(fn (?Subject $record) => $record?->archive_number ?? 'يُولّد تلقائياً عند الحفظ'),

                    Forms\Components\ToggleButtons::make('direction')
                        ->label('اتجاه المستند')
                        ->options(DocumentDirection::class)
                        ->default(DocumentDirection::Incoming->value)
                        ->inline()
                        ->live()
                        ->required(),

                    Forms\Components\Select::make('document_type')
                        ->label('نوع المستند')
                        ->options(DocumentType::class)
                        ->default(DocumentType::Letter->value)
                        ->native(false)
                        ->required(),

                    Forms\Components\TextInput::make('reference_number')
                        ->label(fn (Get $get) => $get('direction') === DocumentDirection::Outgoing->value
                            ? 'رقم الصادر'
                            : 'رقم الوارد')
                        ->placeholder('الرقم المدون على المكاتبة')
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('document_date')
                        ->label('تاريخ المستند')
                        ->default(now())
                        ->maxDate(now())
                        ->native(false)
                        ->displayFormat('Y-m-d')
                        ->required(),

                    Forms\Components\Select::make('confidentiality')
                        ->label('درجة السرية')
                        ->options(Confidentiality::class)
                        ->default(Confidentiality::Normal->value)
                        ->native(false)
                        ->helperText('"سري للغاية" لا يراه إلا من سجّله ومدير الإدارة')
                        ->required(),
                ])->columns(3),

            Forms\Components\Section::make('الموضوع')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('رأس الموضوع / العنوان')
                        ->placeholder('أدخل عنوان الموضوع...')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->label('ملخص الموضوع')
                        ->placeholder('اكتب ملخصاً يسهّل البحث عن المستند لاحقاً...')
                        ->rows(3)
                        ->maxLength(5000)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('tags')
                        ->label('التصنيفات (Tags)')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('مثال: تراخيص، تعديات، موازنة، شكاوى...')
                        ->helperText('اختر تصنيفاً موجوداً أو أضف تصنيفاً جديداً')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('اسم التصنيف')
                                ->required()
                                ->unique('tags', 'name')
                                ->maxLength(100),
                        ])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('الجهة ومكان الحفظ')
                ->icon('heroicon-o-building-library')
                ->schema(array_values(array_filter([
                    $forAdmin
                        ? Forms\Components\Select::make('department_id')
                            ->label('الإدارة مالكة المستند')
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                        : null,

                    Forms\Components\Select::make('source_id')
                        ->label(fn (Get $get) => (DocumentDirection::tryFrom((string) $get('direction')) ?? DocumentDirection::Incoming)->partyLabel() . ' (إدارة داخلية)')
                        ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->requiredWithout('external_party')
                        ->validationMessages(['required_without' => 'اختر إدارة داخلية أو اكتب اسم جهة خارجية']),

                    Forms\Components\TextInput::make('external_party')
                        ->label('أو جهة خارجية')
                        ->placeholder('وزارة / هيئة / محافظة أخرى / مواطن...')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('paper_location')
                        ->label('مكان حفظ الأصل الورقي')
                        ->placeholder('مثال: غرفة 3 - دولاب 2 - رف 4 - ملف 15')
                        ->maxLength(255),
                ])))->columns(2),

            Forms\Components\Section::make('المرفقات الإلكترونية')
                ->icon('heroicon-o-paper-clip')
                ->schema(array_values(array_filter([
                    $forAdmin
                        ? null
                        : Forms\Components\ViewField::make('scan_action')
                            ->view('filament.forms.components.scanner')
                            ->label('المسح الضوئي'),

                    SpatieMediaLibraryFileUpload::make('attachment')
                        ->label('الملفات المرفقة')
                        ->collection('archive_attachments')
                        ->disk('local')
                        ->visibility('private')
                        ->acceptedFileTypes(self::ACCEPTED_FILE_TYPES)
                        ->maxSize(20 * 1024)
                        ->helperText('PDF، صور، Word، Excel - بحد أقصى 20 ميجا للملف')
                        ->multiple()
                        ->reorderable()
                        ->columnSpanFull(),
                ]))),
        ];
    }

    public static function infolist(bool $withTimeline = false): array
    {
        return array_values(array_filter([
            Infolists\Components\Section::make('بيانات القيد')
                ->icon('heroicon-o-identification')
                ->schema([
                    Infolists\Components\TextEntry::make('archive_number')->label('رقم القيد')->copyable()->weight('bold'),
                    Infolists\Components\TextEntry::make('direction')->label('الاتجاه')->badge(),
                    Infolists\Components\TextEntry::make('document_type')->label('نوع المستند'),
                    Infolists\Components\TextEntry::make('reference_number')->label('رقم الصادر/الوارد')->placeholder('-'),
                    Infolists\Components\TextEntry::make('document_date')->label('تاريخ المستند')->date('Y-m-d'),
                    Infolists\Components\TextEntry::make('confidentiality')->label('درجة السرية')->badge(),
                ])->columns(3),

            Infolists\Components\Section::make('الموضوع')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Infolists\Components\TextEntry::make('title')->label('العنوان')->weight('bold')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('description')->label('الملخص')->placeholder('-')->columnSpanFull(),
                    Infolists\Components\TextEntry::make('tags.name')->label('التصنيفات')->badge()->placeholder('-'),
                    Infolists\Components\TextEntry::make('source.name')->label('الجهة الداخلية')->placeholder('-'),
                    Infolists\Components\TextEntry::make('external_party')->label('الجهة الخارجية')->placeholder('-'),
                    Infolists\Components\TextEntry::make('paper_location')->label('مكان الأصل الورقي')->placeholder('-'),
                ])->columns(2),

            Infolists\Components\Section::make('حالة المستند')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Infolists\Components\TextEntry::make('department.name')->label('الإدارة المالكة'),
                    Infolists\Components\TextEntry::make('user.name')->label('سجّله')->placeholder('-'),
                    Infolists\Components\TextEntry::make('created_at')->label('تاريخ الأرشفة')->dateTime('Y-m-d h:i A'),
                    Infolists\Components\IconEntry::make('is_active')->label('ساري')->boolean(),
                    Infolists\Components\TextEntry::make('deactivatedBy.name')->label('ألغاه')->visible(fn (Subject $record) => ! $record->is_active),
                    Infolists\Components\TextEntry::make('deactivated_at')->label('تاريخ الإلغاء')->dateTime('Y-m-d h:i A')->visible(fn (Subject $record) => ! $record->is_active),
                    Infolists\Components\TextEntry::make('deactivation_reason')->label('سبب الإلغاء')->color('danger')->columnSpanFull()->visible(fn (Subject $record) => ! $record->is_active),
                ])->columns(3),

            Infolists\Components\Section::make('المرفقات')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    Infolists\Components\ViewEntry::make('attachments')
                        ->hiddenLabel()
                        ->view('filament.shared.attachments-entry'),
                ]),

            $withTimeline
                ? Infolists\Components\Section::make('دورة عمل المستند')
                    ->icon('heroicon-o-clock')
                    ->description('كل الحركات على المستند: الإنشاء، التعديل، العرض، فتح المرفقات، الإلغاء...')
                    ->collapsible()
                    ->schema([
                        Infolists\Components\ViewEntry::make('timeline')
                            ->hiddenLabel()
                            ->view('filament.shared.subject-timeline-entry'),
                    ])
                : null,
        ]));
    }

    public static function columns(bool $showDepartment = false): array
    {
        return [
            Tables\Columns\TextColumn::make('archive_number')
                ->label('رقم القيد')
                ->searchable()
                ->sortable()
                ->copyable()
                ->fontFamily('mono')
                ->size('sm'),

            Tables\Columns\TextColumn::make('direction')
                ->label('الاتجاه')
                ->badge(),

            Tables\Columns\TextColumn::make('title')
                ->label('الموضوع')
                ->searchable(['title', 'description', 'reference_number', 'external_party'])
                ->sortable()
                ->weight('bold')
                ->color('primary')
                ->description(fn (Subject $record) => $record->document_type?->getLabel())
                ->wrap(),

            Tables\Columns\TextColumn::make('source.name')
                ->label('الجهة')
                ->default(fn (Subject $record) => $record->external_party)
                ->placeholder('-')
                ->searchable()
                ->wrap(),

            Tables\Columns\TextColumn::make('tags.name')
                ->label('التصنيفات')
                ->badge()
                ->color('gray')
                ->searchable()
                ->limitList(3),

            Tables\Columns\TextColumn::make('document_date')
                ->label('تاريخ المستند')
                ->date('Y-m-d')
                ->sortable(),

            Tables\Columns\TextColumn::make('confidentiality')
                ->label('السرية')
                ->badge()
                ->toggleable(),

            Tables\Columns\IconColumn::make('is_active')
                ->label('الحالة')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-no-symbol')
                ->tooltip(fn (Subject $record) => $record->is_active ? 'ساري' : 'ملغي: ' . $record->deactivation_reason),

            Tables\Columns\TextColumn::make('department.name')
                ->label('الإدارة')
                ->badge()
                ->color('gray')
                ->sortable()
                ->visible($showDepartment)
                ->toggleable(),

            Tables\Columns\TextColumn::make('user.name')
                ->label('سجّله')
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الأرشفة')
                ->dateTime('Y-m-d H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @param  \Closure(): array  $usersOptions  قائمة الموظفين لفلتر "سجّله" (null لإخفاء الفلتر)
     */
    public static function filters(bool $showDepartment = false, ?\Closure $usersOptions = null): array
    {
        return array_values(array_filter([
            TernaryFilter::make('is_active')
                ->label('الحالة')
                ->placeholder('الكل')
                ->trueLabel('سارية فقط')
                ->falseLabel('الملغاة فقط')
                ->default(true),

            SelectFilter::make('direction')
                ->label('صادر / وارد')
                ->options(DocumentDirection::class),

            SelectFilter::make('document_type')
                ->label('نوع المستند')
                ->options(DocumentType::class)
                ->multiple(),

            SelectFilter::make('tags')
                ->label('التصنيفات')
                ->relationship('tags', 'name')
                ->multiple()
                ->searchable()
                ->preload(),

            SelectFilter::make('year')
                ->label('السنة')
                ->options(fn () => collect(range(now()->year, now()->year - 30))->mapWithKeys(fn ($y) => [$y => $y])->all())
                ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($q, $y) => $q->whereYear('document_date', $y))),

            SelectFilter::make('month')
                ->label('الشهر')
                ->options(self::MONTHS)
                ->query(fn (Builder $query, array $data) => $query->when($data['value'], fn ($q, $m) => $q->whereMonth('document_date', $m))),

            SelectFilter::make('confidentiality')
                ->label('درجة السرية')
                ->options(Confidentiality::class),

            SelectFilter::make('source_id')
                ->label('الجهة (إدارة داخلية)')
                ->options(fn () => Department::orderBy('name')->pluck('name', 'id'))
                ->searchable(),

            $showDepartment
                ? SelectFilter::make('department_id')
                    ->label('الإدارة مالكة المستند')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                : null,

            $usersOptions
                ? SelectFilter::make('user_id')
                    ->label('الموظف الذي سجّله')
                    ->options($usersOptions)
                    ->searchable()
                : null,

            Tables\Filters\Filter::make('document_date_range')
                ->label('فترة تاريخ المستند')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('من تاريخ'),
                    Forms\Components\DatePicker::make('until')->label('إلى تاريخ'),
                ])
                ->query(fn (Builder $query, array $data) => $query
                    ->when($data['from'], fn ($q, $d) => $q->whereDate('document_date', '>=', $d))
                    ->when($data['until'], fn ($q, $d) => $q->whereDate('document_date', '<=', $d))),
        ]));
    }

    public static function attachmentsAction(): Action
    {
        return Action::make('view_attachments')
            ->label('المرفقات')
            ->icon('heroicon-o-paper-clip')
            ->color('success')
            ->modalHeading(fn (Subject $record) => 'مرفقات: ' . $record->archive_number)
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('إغلاق')
            ->modalWidth('lg')
            ->modalContent(fn (Subject $record) => view('filament.staff.attachments-modal', [
                'media' => $record->getMedia('archive_attachments'),
            ]));
    }

    public static function deactivateAction(): Action
    {
        return Action::make('deactivate')
            ->label('إلغاء التفعيل')
            ->icon('heroicon-o-no-symbol')
            ->color('danger')
            ->visible(fn (Subject $record) => auth()->user()->can('deactivate', $record))
            ->requiresConfirmation()
            ->modalHeading('إلغاء تفعيل المستند')
            ->modalDescription('لن يُحذف المستند، وسيبقى محفوظاً في الأرشيف بحالة "ملغي" ويمكن لمدير الإدارة إعادة تفعيله.')
            ->form([
                Forms\Components\Textarea::make('reason')
                    ->label('سبب إلغاء التفعيل')
                    ->required()
                    ->maxLength(1000),
            ])
            ->action(function (Subject $record, array $data) {
                abort_unless(auth()->user()->can('deactivate', $record), 403);
                $record->deactivate(auth()->user(), $data['reason']);
            })
            ->successNotificationTitle('تم إلغاء تفعيل المستند');
    }

    public static function activateAction(): Action
    {
        return Action::make('activate')
            ->label('إعادة التفعيل')
            ->icon('heroicon-o-arrow-path')
            ->color('success')
            ->visible(fn (Subject $record) => auth()->user()->can('activate', $record))
            ->requiresConfirmation()
            ->modalDescription(fn (Subject $record) => 'سبب الإلغاء: ' . $record->deactivation_reason)
            ->action(function (Subject $record) {
                abort_unless(auth()->user()->can('activate', $record), 403);
                $record->activate();
            })
            ->successNotificationTitle('تمت إعادة تفعيل المستند');
    }

    // تسجيل "عرض بيانات المستند" في سجل الحركات
    public static function logView(Subject $record): void
    {
        ActivityLog::record('viewed', $record);
    }

    public static function usersOf(array $departmentIds): \Closure
    {
        return fn () => User::whereIn('department_id', $departmentIds)->orderBy('name')->pluck('name', 'id')->all();
    }
}
