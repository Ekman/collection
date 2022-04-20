<?php

namespace Nekman\Collection\Tests\Legacy;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

class ObjectBehavior extends TestCase
{
    private Collection $it;

    public function beConstructedWith(iterable|callable $it): Collection
    {
        return $this->it = new Collection($it);
    }

    public function __call(string $name, array $arguments)
    {
        $this->it->$name(...$arguments);
    }
}
