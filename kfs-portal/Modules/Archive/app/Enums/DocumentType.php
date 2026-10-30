<?php

namespace Modules\Archive\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case Letter = 'letter';
    case Memo = 'memo';
    case Decision = 'decision';
    case Circular = 'circular';
    case Complaint = 'complaint';
    case Contract = 'contract';
    case Report = 'report';
    case Minutes = 'minutes';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Letter => 'خطاب / مكاتبة',
            self::Memo => 'مذكرة داخلية',
            self::Decision => 'قرار',
            self::Circular => 'تعميم / منشور',
            self::Complaint => 'شكوى / طلب مواطن',
            self::Contract => 'عقد / اتفاقية',
            self::Report => 'تقرير',
            self::Minutes => 'محضر اجتماع',
            self::Other => 'أخرى',
        };
    }
}
