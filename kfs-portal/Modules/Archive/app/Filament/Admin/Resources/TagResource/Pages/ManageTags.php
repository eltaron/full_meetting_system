<?php

namespace Modules\Archive\Filament\Admin\Resources\TagResource\Pages;

use Modules\Archive\Filament\Admin\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('تصنيف جديد'),
        ];
    }
}
