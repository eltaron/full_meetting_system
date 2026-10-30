<?php

namespace Modules\Archive\Filament\Admin\Resources\SubjectResource\Pages;

use Modules\Archive\Enums\DocumentDirection;
use Modules\Archive\Filament\Admin\Resources\SubjectResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('أرشفة مستند جديد')->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل'),
            'incoming' => Tab::make('الوارد')
                ->icon('heroicon-m-arrow-down-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('direction', DocumentDirection::Incoming)),
            'outgoing' => Tab::make('الصادر')
                ->icon('heroicon-m-arrow-up-tray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('direction', DocumentDirection::Outgoing)),
        ];
    }
}
