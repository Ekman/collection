<?php

namespace DusanKasan\Knapsack;

function compare(mixed $a, mixed $b): int
{
    return \Nekman\Collection\compare($a, $b);
}

function decrement(int|float $value): int|float
{
    return \Nekman\Collection\decrement($value);
}

function identity(mixed $value): mixed
{
    return \Nekman\Collection\identity($value);
}

function increment(int|float $value): int|float
{
    return \Nekman\Collection\increment($value);
}
