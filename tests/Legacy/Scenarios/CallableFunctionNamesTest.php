<?php

namespace Nekman\Collection\Tests\Legacy\Scenarios;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class CallableFunctionNamesTest extends TestCase
{
    public function testIt(): void
    {
        $result = Collection::from([2, 1])
            ->concat([3, 4])
            ->sort("\\Nekman\\Collection\\compare")
            ->values()
            ->toArray();

        $expected = [1, 2, 3, 4];

        $this->assertEquals($expected, $result);
    }
}
