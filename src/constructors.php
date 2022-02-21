<?php

namespace Nekman\Collection;

use Nekman\Collection\Exceptions\NoMoreItems;

function iterable_iterate(mixed $value, callable $iterable): iterable
{
    try {
        while (true) {
            yield $iterable($value);
        }
    } catch (NoMoreItems) {
        // End loop
    }
}

function iterable_repeat(mixed $initial, int $nItems = -1): iterable
{
    for ($i = 0; $nItems === -1 || $i < $nItems; $i++) {
        yield $initial;
    }
}

function iterable_range(int $start = 0, ?int $end = null, int $step = 1): iterable
{
    for ($i = $start; $end === null || $i <= $end; $i += $step) {
        yield $i;
    }
}
