<?php

namespace Modules\Archive\Filament\Admin\Resources\UserResource\Pages;

use Modules\Archive\Enums\UserRole;
use Modules\Archive\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->is(auth()->user()) || $this->getRecord()->subjects()->withTrashed()->exists()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // مدير المنظومة لا يتبع إدارة
        if (($data['role'] ?? null) === UserRole::Admin->value) {
            $data['department_id'] = null;
        }

        return $data;
    }
}
