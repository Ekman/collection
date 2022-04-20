<?php

namespace Nekman\Collection\Tests\Legacy\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class FibonacciSequenceTest extends TestCase
{
    public function testIt(): void
    {
        $result = Collection::iterate([1, 1], fn ($v) => [$v[1], $v[0] + $v[1]])
            ->map("\\Nekman\\Collection\\iterable_first")
            ->take(5)
            ->values()
            ->toArray();

        $this->assertEquals([1, 1, 2, 3, 5], $result);
    }
}
