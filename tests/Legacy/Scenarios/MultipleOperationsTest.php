<?php

namespace Nekman\Collection\Tests\Legacy\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class MultipleOperationsTest extends TestCase
{
    public function testIt(): void
    {
        $array = [1, 2, 8, 3, 7, 5, 1, 4, 4,];
        $collection = new Collection($array);

        $result = $collection
            ->reject(fn ($v) => $v > 2)
            ->filter(fn ($k) => $k > 5)
            ->distinct()
            ->concat([1, 2])
            ->map(fn ($i) => [$i, $i + 1])
            ->flatten()
            ->sort(fn ($a, $b) => $a > $b)
            ->slice(2, 5)
            ->groupBy(fn ($v) => $v % 2 == 0 ? "even" : "odd")
            ->get("even")
            ->toArray();

        $this->assertEquals([2], $result);
    }
}
