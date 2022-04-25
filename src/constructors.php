<?php

namespace Nekman\Collection;

use DeepCopy\DeepCopy;
use Nekman\Collection\Exceptions\NoMoreItems;

function iterable_iterate(mixed $value, callable $iterate): iterable
{
    $duplicated = (new DeepCopy)->copy($value);

    $value = $duplicated;

    yield $value;

    while (true) {
        try {
            $value = $iterate($value);
            yield $value;
        } catch (NoMoreItems) {
            break;
        }
    }
}

function iterable_repeat(mixed $startValue, int $nItems = -1): iterable
{
    for ($i = 0; $nItems === -1 || $i < $nItems; $i++) {
        yield $startValue;
    }
}

function iterable_range(int $start = 0, ?int $end = null, int $step = 1): iterable
{
    for ($i = $start; $end === null || $i <= $end; $i += $step) {
        yield $i;
    }
}
