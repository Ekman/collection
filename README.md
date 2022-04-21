# Collection

[![Build Status](https://circleci.com/gh/Ekman/collection.svg?style=svg)](https://app.circleci.com/pipelines/github/Ekman/collection)
[![Coverage Status](https://coveralls.io/repos/github/Ekman/collection/badge.svg?branch=master)](https://coveralls.io/github/Ekman/collection?branch=master)

This is a zero dependency collection library that is heavily inspired
by [Clojure sequences](https://clojure.org/reference/sequences). It is closely integrated with the native PHP
type [`iterable`](https://www.php.net/manual/en/language.types.iterable.php).

## Installation

Install with [Composer](https://getcomposer.org/):

```bash
composer require nekman/collection
```

## DusanKasan Knapsack

The base of this project originates from [dusankasan/knapsack](https://github.com/DusanKasan/Knapsack)
. `nekman/collection:^1` aims to be 100% interchangeable with `dusankasan/knapsack:10.*`. Have an existing project that
utilizes [dusankasan/knapsack](https://github.com/DusanKasan/Knapsack)? You can switch to this library without having to
do any rewrites.

We appreciate all the hard work that has been put into [dusankasan/knapsack](https://github.com/DusanKasan/Knapsack).
Knapsack is a great library created for a programming language that lacks native collection handling in the standard
library. Our aim is to keep the torch alive and to maintain it for current and future PHP version so that the code lives
on.

## Usage

You can use this library object-oriented or using functions.

Example using object-oriented:

```php
use Nekman\Collection\{Collection, Contracts\CollectionInterface};

$result = Collection::from([1, 2, 3, 4, 5])
    ->map(fn (int $number) => $number * 2)
    ->partition(2)
    ->filter(fn (CollectionInterface $collection) => $collection->size() < 10)
    ->join(", ");

echo $result; // 2, 4, 6, 8
```

Example using functions:

```php
use function \Nekman\Collection\{iterable_map, iterable_partition, iterable_filter, iterable_size, iterable_join};

$array = [1, 2, 3, 4, 5];
$mapped = iterable_map($array, fn (int $number) => $number * 2);
$partitioned = iterable_partition($mapped, 2);
$filtered = iterable_filter($partitioned, fn (iterable $it) => iterable_size($it) < 10);

echo iterable_join($filtered, ", "); // 2, 4, 6, 8
```

For a complete list of available functions, see [functions](src/operations.php)
or [`CollectionInterface`](src/Contracts/CollectionInterface.php).

## Versioning

This project complies with [Semantic Versioning](https://semver.org/).

## Changelog

For a complete list of changes, and how to migrate between major versions,
see [releases page](https://github.com/Ekman/collection/releases).
