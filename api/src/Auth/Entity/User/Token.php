<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
final class Token
{
    public function __construct(
        /**
         * @ORM\Column(type="string", nullable=true)
         */
        private string $value,
        /**
         * @ORM\Column(type="datetime_immutable", nullable=true)
         */
        private readonly DateTimeImmutable $expires
    ) {
        Assert::uuid($value);
        $this->value = mb_strtolower($value);
    }

    public function validate(string $value, DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($value)) {
            throw new DomainException('Token is invalid.');
        }
        if ($this->isExpiredTo($date)) {
            throw new DomainException('Token is expired.');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getExpires(): DateTimeImmutable
    {
        return $this->expires;
    }

    public function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    private function isEqualTo(string $value): bool
    {
        return $this->value === $value;
    }
}
