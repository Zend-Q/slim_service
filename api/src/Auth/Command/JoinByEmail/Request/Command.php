<?php

declare(strict_types=1);

namespace App\Auth\Command\JoinByEmail\Request;

final class Command
{
    public string $email = '';
    public string $password = '';
}
