<?php

namespace Modules\Archive\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasLabel, HasColor
{
    case Admin = 'admin';       // مدير المنظومة (لوحة الإدارة العامة)
    case Manager = 'manager';   // مدير إدارة / رئيس قسم (يتابع موظفي إدارته)
    case Employee = 'employee'; // موظف أرشيف

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'مدير المنظومة',
            self::Manager => 'مدير إدارة / رئيس قسم',
            self::Employee => 'موظف أرشيف',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Manager => 'warning',
            self::Employee => 'gray',
        };
    }
}
