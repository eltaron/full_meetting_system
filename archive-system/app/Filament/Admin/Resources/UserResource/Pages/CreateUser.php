<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // مدير المنظومة لا يتبع إدارة
        if (($data['role'] ?? null) === UserRole::Admin->value) {
            $data['department_id'] = null;
        }

        return $data;
    }
}
