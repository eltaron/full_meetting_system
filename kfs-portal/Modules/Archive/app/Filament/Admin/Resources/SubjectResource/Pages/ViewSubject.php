<?php

namespace Modules\Archive\Filament\Admin\Resources\SubjectResource\Pages;

use Modules\Archive\Filament\Admin\Pages\DocumentLifecycle;
use Modules\Archive\Filament\Admin\Resources\SubjectResource;
use Modules\Archive\Filament\Shared\SubjectSchema;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubject extends ViewRecord
{
    protected static string $resource = SubjectResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        SubjectSchema::logView($this->getRecord());
    }

    public function getTitle(): string
    {
        return $this->getRecord()->archive_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lifecycle')
                ->label('دورة عمل المستند')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->url(DocumentLifecycle::getUrl(['subject' => $this->getRecord()->id])),
            Actions\EditAction::make()->label('تعديل'),
            Actions\RestoreAction::make()->label('استرجاع'),
        ];
    }
}
