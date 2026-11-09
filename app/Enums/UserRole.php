<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Cashier = 'cashier';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
