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

function duplicate(mixed $input): mixed
{
    try {
        if (is_array($input)) {
            return array_map(fn($item) => duplicate($item), $input);
        }

        if (is_object($input) && (new ReflectionClass($input))->isCloneable()) {
            return clone $input;
        }

        if (is_iterable($input)) {
            return iterable_map($input, fn($item) => duplicate($input));
        }
    } catch (ReflectionException) {
        // do nothing
    }

    return $input;
}
