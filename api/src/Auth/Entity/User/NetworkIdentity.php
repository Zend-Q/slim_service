<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Webmozart\Assert\Assert;

final class NetworkIdentity
{
    public function __construct(private string $network, private string $identity)
    {
        Assert::notEmpty($network);
        Assert::notEmpty($identity);
        $this->network = mb_strtolower($network);
        $this->identity = mb_strtolower($identity);
    }

    public function isEqualTo(self $network): bool
    {
        return
            $this->getNetwork() === $network->getNetwork() &&
            $this->getIdentity() === $network->getIdentity();
    }

    public function getNetwork(): string
    {
        return $this->network;
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
