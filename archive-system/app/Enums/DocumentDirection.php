<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum DocumentDirection: string implements HasLabel, HasColor, HasIcon
{
    case Incoming = 'incoming'; // وارد
    case Outgoing = 'outgoing'; // صادر

    public function getLabel(): string
    {
        return match ($this) {
            self::Incoming => 'وارد',
            self::Outgoing => 'صادر',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Incoming => 'info',
            self::Outgoing => 'success',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Incoming => 'heroicon-m-arrow-down-tray',
            self::Outgoing => 'heroicon-m-arrow-up-tray',
        };
    }

    // تسمية حقل الجهة حسب اتجاه المستند
    public function partyLabel(): string
    {
        return match ($this) {
            self::Incoming => 'الجهة الوارد منها',
            self::Outgoing => 'الجهة الصادر إليها',
        };
    }
}
