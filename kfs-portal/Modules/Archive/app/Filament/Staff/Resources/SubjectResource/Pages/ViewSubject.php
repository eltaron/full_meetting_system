<?php

namespace Modules\Archive\Filament\Staff\Resources\SubjectResource\Pages;

use Modules\Archive\Filament\Shared\SubjectSchema;
use Modules\Archive\Filament\Staff\Resources\SubjectResource;
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
            Actions\EditAction::make()->label('تعديل'),
        ];
    }
}
