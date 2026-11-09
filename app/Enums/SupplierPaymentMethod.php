<?php

namespace App\Enums;

enum SupplierPaymentMethod: string
{
    case Cash = 'cash';
    case Bank = 'bank';
    case MobileBanking = 'mobile_banking';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
