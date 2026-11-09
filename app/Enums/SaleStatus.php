<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Completed = 'completed';
    case PartiallyReturned = 'partially_returned';
    case Returned = 'returned';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
