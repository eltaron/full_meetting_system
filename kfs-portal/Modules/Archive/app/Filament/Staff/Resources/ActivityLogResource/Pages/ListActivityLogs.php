<?php

namespace Modules\Archive\Filament\Staff\Resources\ActivityLogResource\Pages;

use Modules\Archive\Filament\Staff\Resources\ActivityLogResource;
use Filament\Resources\Pages\ListRecords;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;
}
