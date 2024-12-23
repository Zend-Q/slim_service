<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

final class Network
{
    public function __construct(private string $name, private string $identity)
    {
        Assert::notEmpty($name);
        Assert::notEmpty($identity);
        $this->name     = mb_strtolower($name);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getName() === $network->getName() &&
            $this->getIdentity() === $network->getIdentity();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
