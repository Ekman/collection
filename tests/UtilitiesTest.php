<?php

namespace Nekman\Collection\Tests;

use PHPUnit\Framework\TestCase;
use function Nekman\Collection\compare;
use function Nekman\Collection\decrement;
use function Nekman\Collection\identity;
use function Nekman\Collection\increment;

final class UtilitiesTest extends TestCase
{
    /** @dataProvider provideCompare */
    public function testCompare(mixed $a, mixed $b, int $expect): void
    {
        $this->assertEquals($expect, compare($a, $b));
    }

    public function provideCompare(): array
    {
        return [
            [1, 2, -1],
            [2, 1, 1],
            [2, 2, 0],
        ];
    }

    public function testDecrement(): void
    {
        $this->assertEquals(2, decrement(3));
    }

    public function testIncrement(): void
    {
        $this->assertEquals(4, increment(3));
    }

    public function testIdentity(): void
    {
        $this->assertEquals(4, identity(4));
    }
}
