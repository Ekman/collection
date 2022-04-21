<?php

namespace Nekman\Collection\Tests\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;
use function Nekman\Collection\iterable_first;

final class FibonacciSequenceTest extends TestCase
{
    public function testIt(): void
    {
        $this->markTestSkipped();

        $result = Collection::iterate([1, 1], fn ($v) => [$v[1], $v[0] + $v[1]])
            ->map(fn ($v) => iterable_first($v))
            ->take(5)
            ->toArray(true);

        $this->assertEquals([1, 1, 2, 3, 5], $result);
    }
}
