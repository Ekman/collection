<?php

namespace Nekman\Collection;

use ReflectionClass;
use ReflectionException;

function compare(mixed $a, mixed $b): int
{
    if ($a == $b) {
        return 0;
    }

    return $a < $b ? -1 : 1;
}

function decrement(int|float $value): int|float
{
    return $value - 1;
}

function identity(mixed $value): mixed
{
    return $value;
}

function increment(int|float $value): int|float
{
    return $value + 1;
}
