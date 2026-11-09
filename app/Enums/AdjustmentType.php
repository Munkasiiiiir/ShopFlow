<?php

namespace App\Enums;

enum AdjustmentType: string
{
    case Damaged = 'damaged';
    case Lost = 'lost';
    case Correction = 'correction';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
