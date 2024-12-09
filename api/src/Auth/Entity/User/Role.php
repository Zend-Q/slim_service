<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

enum Role: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
