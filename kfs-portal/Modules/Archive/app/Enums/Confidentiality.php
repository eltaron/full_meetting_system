<?php

namespace Modules\Archive\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Confidentiality: string implements HasLabel, HasColor
{
    case Normal = 'normal';
    case Confidential = 'confidential';
    case TopSecret = 'top_secret'; // يراه فقط من سجّله ومدير الإدارة ومدير المنظومة

    public function getLabel(): string
    {
        return match ($this) {
            self::Normal => 'عادي',
            self::Confidential => 'سري',
            self::TopSecret => 'سري للغاية',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Normal => 'gray',
            self::Confidential => 'warning',
            self::TopSecret => 'danger',
        };
    }
}
