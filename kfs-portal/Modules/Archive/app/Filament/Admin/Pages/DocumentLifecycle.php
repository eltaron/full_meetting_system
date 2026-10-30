<?php

namespace Modules\Archive\Filament\Admin\Pages;

use Modules\Archive\Models\ActivityLog;
use Modules\Archive\Models\Subject;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;

/**
 * دورة عمل المستند (Tracking): من أنشأه، من عدّله وماذا غيّر، من عرضه وفتح مرفقاته، من ألغاه أو حذفه...
 */
class DocumentLifecycle extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'دورة عمل المستند';
    protected static ?string $navigationGroup = 'الأرشيف';
    protected static ?string $title = 'دورة عمل المستند';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.admin.pages.document-lifecycle';

    #[Url]
    public ?string $subject = null;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['subject_id' => $this->subject]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subject_id')
                    ->label('اختر المستند')
                    ->placeholder('ابحث برقم القيد أو العنوان أو رقم الوارد/الصادر...')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => Subject::withTrashed()
                        ->where(fn ($q) => $q
                            ->where('archive_number', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%")
                            ->orWhere('reference_number', 'like', "%{$search}%"))
                        ->latest('id')
                        ->limit(30)
                        ->get()
                        ->mapWithKeys(fn (Subject $s) => [$s->id => "{$s->archive_number} - {$s->title}"]))
                    ->getOptionLabelUsing(fn ($value) => ($s = Subject::withTrashed()->find($value)) ? "{$s->archive_number} - {$s->title}" : null)
                    ->live()
                    ->afterStateUpdated(fn ($state) => $this->subject = $state),
            ])
            ->statePath('data');
    }

    public function getRecord(): ?Subject
    {
        return $this->subject
            ? Subject::withTrashed()->with(['user', 'department', 'source', 'tags', 'deactivatedBy'])->find($this->subject)
            : null;
    }

    public function getLogs(): Collection
    {
        return $this->subject
            ? ActivityLog::with(['user', 'department'])->where('subject_id', $this->subject)->latest('id')->get()
            : collect();
    }
}
