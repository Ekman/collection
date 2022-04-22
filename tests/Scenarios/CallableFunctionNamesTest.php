<?php

namespace Nekman\Collection\Tests\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;
use function Nekman\Collection\compare;

final class CallableFunctionNamesTest extends TestCase
{
    public function testIt(): void
    {
        $result = Collection::from([2, 1])
            ->concat([3, 4])
            ->sort(fn ($a, $b) => compare($a, $b))
            ->values()
            ->toArray();

        $expected = [1, 2, 3, 4];

        $this->assertEquals($expected, $result);
    }
}
