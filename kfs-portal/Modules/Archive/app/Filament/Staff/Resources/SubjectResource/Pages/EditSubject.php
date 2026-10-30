<?php

namespace Modules\Archive\Filament\Staff\Resources\SubjectResource\Pages;

use Modules\Archive\Filament\Staff\Resources\SubjectResource;
use Filament\Resources\Pages\EditRecord;

class EditSubject extends EditRecord
{
    protected static string $resource = SubjectResource::class;

    // لا يوجد زر حذف هنا: الموظف يلغي التفعيل فقط من قائمة الأرشيف

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
