<?php

namespace App\Filament\Staff\Resources\SubjectResource\Pages;

use App\Filament\Staff\Resources\SubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;

    // الموظف والإدارة يُحددان من الحساب الحالي ولا يمكن تغييرهما من النموذج
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['department_id'] = auth()->user()->department_id;

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'تمت أرشفة المستند برقم قيد: ' . $this->getRecord()->archive_number;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
