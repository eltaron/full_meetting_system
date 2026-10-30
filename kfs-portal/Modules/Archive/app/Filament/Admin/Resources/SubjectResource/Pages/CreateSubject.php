<?php

namespace Modules\Archive\Filament\Admin\Resources\SubjectResource\Pages;

use Modules\Archive\Filament\Admin\Resources\SubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تمت أرشفة المستند برقم قيد: ' . $this->getRecord()->archive_number;
    }
}
