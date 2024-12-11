<?php

declare(strict_types=1);

namespace App\Auth\Command\ChangeRole;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Auth\Entity\User\UserRepository;
use App\Flusher;
use DomainException;

final readonly class Handler
{
    public function __construct(
        private UserRepository $users,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): void
    {
        $user = $this->users->get(new Id($command->id));
        $role = Role::tryFrom($command->role);

        if ($role === null) {
            throw new DomainException('Trying create from an invalid role');
        }

        $user->changeRole(
            $role
        );
        $this->flusher->flush();
    }
}