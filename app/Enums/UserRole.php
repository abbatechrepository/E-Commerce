<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case CUSTOMER = 'customer';

    public function isAdministrative(): bool
    {
        return in_array($this, [self::ADMIN, self::MANAGER], true);
    }
}
