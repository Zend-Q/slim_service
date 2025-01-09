<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Entity\User;

use App\Auth\Entity\User\Status;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Status
 * @internal
 */
final class StatusTest extends TestCase
{
    public function testSuccess(): void
    {
        $status = Status::WAIT;
        self::assertEquals(Status::WAIT->name, $status->name);
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $status = Status::tryFrom('none');
    }

    public function testWait(): void
    {
        $status = Status::WAIT;
        self::assertTrue($status->isWait());
        self::assertFalse($status->isActive());
    }

    public function testActive(): void
    {
        $status = Status::ACTIVE;
        self::assertFalse($status->isWait());
        self::assertTrue($status->isActive());
    }
}
