<?php

namespace App\Enums;

enum AccountStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Inactive = 'inactive';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
