<?php

namespace Modules\Meetings\Filament\Resources\RoomResource\Pages;

use Modules\Meetings\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRoom extends CreateRecord
{
    protected static string $resource = RoomResource::class;
}
