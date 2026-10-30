<?php

namespace Modules\Meetings\Filament\Resources\CategoryResource\Pages;

use Modules\Meetings\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
