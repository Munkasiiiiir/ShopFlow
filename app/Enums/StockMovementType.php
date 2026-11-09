<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Purchase = 'purchase';
    case Sale = 'sale';
    case SalesReturn = 'return';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
