<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

enum Status: string
{
    case WAIT = 'wait';
    case ACTIVE = 'active';

    public function isWait(): bool
    {
        return $this->value === self::WAIT->value;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE->value;
    }
}
