<?php

namespace Nekman\Collection;

function identity(mixed $value): mixed
{
    return $value;
}

function compare(mixed $a, mixed $b): int
{
    if ($a == $b) {
        return 0;
    }

    return $a < $b ? -1 : 1;
}

function increment(int|float $value): int|float
{
    return $value + 1;
}

function decrement(int|float $value): int|float
{
    return $value - 1;
}
