<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case MobileBanking = 'mobile_banking';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
