<?php

namespace Nekman\Collection\Tests;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Nekman\Collection\Collection;
use Nekman\Collection\Contracts\CollectionInterface;
use Nekman\Collection\Exceptions\InvalidArgument;
use Nekman\Collection\Exceptions\InvalidReturnValue;
use Nekman\Collection\Exceptions\ItemNotFound;
use Nekman\Collection\Exceptions\NoMoreItems;
use Nekman\Collection\Tests\Helpers\Car;
use PHPUnit\Framework\TestCase;
use Traversable;
use function Nekman\Collection\compare;
use function Nekman\Collection\iterable_concat;
use function Nekman\Collection\iterable_to_array;

final class FunctionsTest extends TestCase
{
    public function provideAppend(): array
    {
        return [
            [
                [1, 3, 3, 2],
                1,
                null,
                [1, 3, 3, 2, 1],
            ],
            [
                [1, 3, 3, 2],
                1,
                "a",
                [1, 3, 3, 2, "a" => 1],
            ],
        ];
    }

    public function provideAverage(): array
    {
        return [
            "Test int" => [
                [1, 2, 3],
                2,
            ],
            "Test float" => [
                [6.4, 3.2, 8.9],
                6.166666666666667,
            ],
            [
                [],
                0,
            ],
        ];
    }

    public function provideCombine(): array
    {
        return [
            [
                ["a", "b"],
                [1, 2],
                ["a" => 1, "b" => 2],
            ],
            [
                ["a", "b"],
                [1],
                ["a" => 1],
            ],
            [
                ["a", "b"],
                [1, 2, 3],
                ["a" => 1, "b" => 2],
            ],
        ];
    }

    public function provideConcat(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [
                    [4, 5],
                ],
                [4, 5, 3, 2],
            ],
        ];
    }

    public function provideContains(): array
    {
        return [
            [
                [1, 3, 3, 2],
                3,
                true,
            ],
            [
                [1, 3, 3, 2],
                true,
                false,
            ],
        ];
    }

    public function provideCountBy(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v % 2 == 0 ? "even" : "odd",
                ["odd" => 3, "even" => 1],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => ($k + $v) % 2 == 0 ? "even" : "odd",
                ["odd" => 3, "even" => 1],
            ],
        ];
    }

    public function provideCycle(): array
    {
        return [
            [
                [1, 3, 3, 2],
                8,
                [1, 3, 3, 2, 1, 3, 3, 2],
            ],
        ];
    }

    public function provideDereferenceKeyValue(): array
    {
        return [
            [
                [[0, "a"], [1, "b"]],
                ["a", "b"],
            ],
        ];
    }

    public function provideDiff(): array
    {
        return [
            [
                [1, 2, 3, 4],
                [
                    [1, 2],
                ],
                [2 => 3, 3 => 4],
            ],
            [
                [1, 2, 3, 4],
                [
                    [1, 2],
                    [3],
                ],
                [3 => 4],
            ],
        ];
    }

    public function provideDistinct(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [1, 3, 3 => 2],
            ],
        ];
    }

    public function provideDrop(): array
    {
        return [
            [
                [1, 3, 3, 2],
                2,
                [2 => 3, 3 => 2],
            ],
        ];
    }

    public function provideDropLast(): array
    {
        return [
            [
                [1, 3, 3, 2],
                1,
                [1, 3, 3],
            ],
            [
                [1, 3, 3, 2],
                2,
                [1, 3],
            ],
        ];
    }

    public function provideDropWhile(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v < 3,
                [1 => 3, 2 => 3, 3 => 2],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $k < 2 && $v < 3,
                [1 => 3, 2 => 3, 3 => 2],
            ],
        ];
    }

    public function provideDump(): array
    {
        return [
            [
                [
                    [
                        [1, [2], 3],
                        ["a" => "b"],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(["a", "b", "c"]),
                    true,
                    new Car("sedan", 5),
                    iterable_concat([1], [1]),
                ],
                null,
                null,
                [
                    [
                        [1, [2], 3],
                        ["a" => "b"],
                        [1, 2, 3],
                    ],
                    [1, 2, 3],
                    ["a", "b", "c"],
                    true,
                    [
                        "Nekman\\Collection\\Tests\\Helpers\\Car" => [
                            "numberOfSeats" => 5,
                        ],

                    ],
                    [1, "0//1" => 1],
                ],
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ["a" => "b"],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(["a", "b", "c"]),
                    true,
                    new Car("sedan", 5),
                    iterable_concat([1], [1]),
                ],
                2,
                null,
                [
                    [
                        [1, [2], ">>>"],
                        ["a" => "b"],
                        ">>>",
                    ],
                    [1, 2, ">>>"],
                    ">>>",
                ],
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ["a" => "b"],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(["a", "b", "c"]),
                    true,
                    new Car("sedan", 5),
                    iterable_concat([1], [1]),
                ],
                null,
                3,
                [
                    [
                        [1, "^^^", 3],
                        ["a" => "b"],
                        [1, 2, 3],
                    ],
                    [1, 2, 3],
                    ["a", "b", "c"],
                    true,
                    [
                        "Nekman\\Collection\\Tests\\Helpers\\Car" => [
                            "numberOfSeats" => 5,
                        ],
                    ],
                    [1, "0//1" => 1],
                ],
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ["a" => "b"],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(["a", "b", "c"]),
                    true,
                    new Car("sedan", 5),
                    iterable_concat([1], [1]),
                ],
                2,
                3,
                [
                    [
                        [1, "^^^", ">>>"],
                        ["a" => "b"],
                        ">>>",
                    ],
                    [1, 2, ">>>"],
                    ">>>",
                ],
            ],
        ];
    }

    public function provideEvery(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v > 0,
                true,
            ],
            [
                [1, 3, 3, 2],
                fn ($v) => $v > 1,
                false,
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $v > 0 && $k >= 0,
                true,
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $v > 0 && $k > 0,
                false,
            ],
        ];
    }

    public function provideExcept(): array
    {
        return [
            [
                ["a" => 1, "b" => 2],
                ["a", "b"],
                [],
            ],
        ];
    }

    public function provideExtract(): array
    {
        return [
            [
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
                "",
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
            ],
            [
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
                "a.b",
                [1, 2],
            ],
            [
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
                "*.b",
                [1, 2, 3, 4, 5],
            ],
            [
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
                "\\*.b",
                [3],
            ],
            [
                [
                    [
                        "a" => [
                            "b" => 1,
                        ],
                    ],
                    [
                        "a" => [
                            "b" => 2,
                        ],
                    ],
                    [
                        "*" => [
                            "b" => 3,
                        ],
                    ],
                    [
                        "." => [
                            "b" => 4,
                        ],
                        "c" => [
                            "b" => 5,
                        ],
                        [
                            "a",
                        ],
                    ],
                ],
                "\\..b",
                [4],
            ],
        ];
    }

    public function provideFilter(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($item) => $item > 2,
                [1 => 3, 2 => 3],
            ],
            [
                [1, 3, 3, 2],
                fn ($item, $key) => $key > 2 && $item < 3,
                [3 => 2],
            ],
        ];
    }

    public function provideFind(): array
    {
        return [
            [
                [1, 3, 3, 2, [5]],
                fn ($v) => $v < 3,
                null,
                1,
            ],
            [
                [1, 3, 3, 2, [5]],
                fn ($v) => $v < 0,
                null,
                null,
            ],
            [
                [1, 3, 3, 2, [5]],
                fn ($v) => $v < 0,
                null,
                null,
            ],
            [
                [1, 3, 3, 2, [5]],
                fn ($v) => $v < 0,
                "not found",
                "not found",
            ],
        ];
    }

    public function provideFirst(): array
    {
        return [
            [
                [1, [2], 3],
                false,
                1,
            ],
            [
                [1, [2], 3],
                true,
                [1],
            ],
        ];
    }

    public function provideFirst_fail(): array
    {
        return [
            [
                [],
                ItemNotFound::class,
            ],
        ];
    }

    public function provideFlatten(): array
    {
        return [
            [
                [1, [2, [3]]],
                -1,
                [1, 2, 3],
            ],
            [
                [1, [2, [3]]],
                1,
                [1, 2, [3]],
            ],
        ];
    }

    public function provideFlip(): array
    {
        return [
            [
                ["a" => 1, "b" => 2],
                [1 => "a", 2 => "b"],
            ],
        ];
    }

    public function provideFrequencies(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [1 => 1, 3 => 2, 2 => 1],
            ],
        ];
    }

    public function provideFromAndCreate(): array
    {
        return [
            "Test array" => [
                [1, 2, 3],
                [1, 2, 3],
            ],
            "Test iterable" => [
                new ArrayIterator([1, 2, 3, 4]),
                [1, 2, 3, 4],
            ],
            "Test generator function" => [
                fn () => yield from [1, 2],
                [1, 2],
            ],
            "Test generator function, return iterable" => [
                fn () => new ArrayIterator([1, 2]),
                [1, 2],
            ],
            "Test generator function, return collection" => [
                fn () => Collection::from([1, 2]),
                [1, 2],
            ],
            "Test iterable function" => [
                fn () => [3, 4, 5],
                [3, 4, 5],
            ],
            "Test collection" => [
                Collection::from(["foo", "bar"]),
                ["foo", "bar"],
            ],
        ];
    }

    public function provideFromAndCreate_fail(): array
    {
        return [
            [
                fn () => 1,
                InvalidArgument::class,
            ],
        ];
    }

    public function provideGet(): array
    {
        return [
            [
                [1, [2], 3],
                0,
                false,
                1,
            ],
            [
                [1, [2], 3],
                1,
                true,
                [2],
            ],
            [
                [1, [2], 3],
                1,
                false,
                [2],
            ],
        ];
    }

    public function provideGetOrDefault(): array
    {
        return [
            [
                [1, [2], 3],
                0,
                null,
                false,
                1,
            ],
            [
                [1, [2], 3],
                1,
                null,
                true,
                [2],
            ],
            [
                [1, [2], 3],
                1,
                null,
                false,
                [2],
            ],
            [
                [1, [2], 3],
                5,
                null,
                false,
                null,
            ],
            [
                [1, [2], 3],
                5,
                "not found",
                false,
                "not found",
            ],
        ];
    }

    public function provideGet_fail(): array
    {
        return [
            [
                [1, 3, 3, 2],
                5,
                ItemNotFound::class,
            ],
        ];
    }

    public function provideGroupBy(): array
    {
        return [
            [
                [1, 2, 3, 4, 5],
                fn ($i) => $i % 2,
                [
                    [2, 4],
                    [1, 3, 5],
                ],
            ],
            [
                [1, 2, 3, 4, 5],
                fn ($i, $k) => ($k + $i) % 3,
                [
                    [2, 5],
                    [1, 4],
                    [3],
                ],
            ],
        ];
    }

    public function provideGroupByKey(): array
    {
        return [
            [
                [
                    "some" => "thing",
                    ["letter" => "A", "type" => "caps"],
                    ["letter" => "a", "type" => "small"],
                    ["letter" => "B", "type" => "caps"],
                    ["letter" => "Z"],
                ],
                "type",
                [
                    ["letter" => "A", "type" => "caps"],
                    ["letter" => "a", "type" => "small"],
                    ["letter" => "B", "type" => "caps"],
                ],
            ],
        ];
    }

    public function provideHas(): array
    {
        return [
            [
                ["a" => 1, "b" => 2],
                "a",
                true,
            ],
            [
                ["a" => 1, "b" => 2],
                "x",
                false,
            ],
        ];
    }

    public function provideIndexBy(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v,
                [1 => 1, 3 => 3, 2 => 2],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $k . $v,
                ["01" => 1, "13" => 3, "23" => 3, "32" => 2],
            ],
        ];
    }

    public function provideInterleave(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [
                    ["a", "b", "c", "d", "e"],
                ],
                [1, "a", 3, "b", 3, "c", 2, "d", "e"],
            ],
        ];
    }

    public function provideInterpose(): array
    {
        return [
            [
                [1, 3, 3, 2],
                "a",
                [1, "a", 3, "a", 3, "a", 2],
            ],
        ];
    }

    public function provideIntersect(): array
    {
        return [
            [
                [1, 2, 3],
                [
                    [1, 2],
                ],
                [1, 2],
            ],
            [
                [1, 2, 3],
                [
                    [1],
                    [3],
                ],
                [1, 3],
            ],
        ];
    }

    public function provideIsEmpty(): array
    {
        return [
            [
                [],
                true,
            ],
        ];
    }

    public function provideIsNotEmpty(): array
    {
        return [
            [
                [1, 3, 3, 2],
                true,
            ],
        ];
    }

    public function provideKeys(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [0, 1, 2, 3],
            ],
        ];
    }

    public function provideLast(): array
    {
        return [
            [
                [1, [2], 3],
                false,
                3,
            ],
            [
                [1, [2], 3],
                true,
                [3],
            ],
        ];
    }

    public function provideLast_fail(): array
    {
        return [
            [
                [],
                ItemNotFound::class,
            ],
        ];
    }

    public function provideMap(): array
    {
        return [
            [
                [1, 2, 3],
                fn ($number) => $number * 2,
                [2, 4, 6],
            ],
        ];
    }

    public function provideMapcat(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => [[$v]],
                [[1], [3], [3], [2]],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => [[$k + $v]],
                [[1], [4], [5], [5]],
            ],
        ];
    }

    public function provideMax(): array
    {
        return [
            [
                [1, 3, 3, 2],
                3,
            ],
            [
                [],
                null,
            ],
        ];
    }

    public function provideMin(): array
    {
        return [
            [
                [2, 1, 3, 2],
                1,
            ],
            [
                [],
                null,
            ],
        ];
    }

    public function provideOnly(): array
    {
        return [
            [
                ["a" => 1, "b" => 2, "c" => 3],
                ["a", "b"],
                ["a" => 1, "b" => 2],
            ],
            [
                ["a" => 1, "b" => 2, "c" => 3],
                ["a", "b", "x"],
                ["a" => 1, "b" => 2],
            ],
        ];
    }

    public function providePartition(): array
    {
        return [
            [
                [1, 3, 3, 2],
                3,
                [
                    [1, 3, 3],
                    [3 => 2],
                ],
            ],
            [
                [1, 3, 3, 2],
                2,
                [
                    [1, 3],
                    [2 => 3, 3 => 2],
                ],
            ],
        ];
    }

    public function providePartitionBy(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v % 3 == 0,
                [
                    [1],
                    [1 => 3, 2 => 3],
                    [3 => 2],
                ],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $k - $v,
                [
                    [1],
                    [1 => 3],
                    [2 => 3],
                    [3 => 2],
                ],
            ],
        ];
    }

    public function providePrepend(): array
    {
        return [
            [
                [1, 3, 3, 2],
                1,
                null,
                [0 => 1, 1 => 3, 2 => 3, 3 => 2],
            ],
            [
                [1, 3, 3, 2],
                1,
                "a",
                ["a" => 1, 0 => 1, 1 => 3, 2 => 3, 3 => 2],
            ],
        ];
    }

    public function providePrintDump(): array
    {
        return [
            [
                [1, [2], 3],
            ],
        ];
    }

    public function provideRange(): array
    {
        return [
            [
                5,
                null,
                2,
                [5, 6],
            ],
            [
                5,
                6,
                4,
                [5, 6],
            ],
        ];
    }

    public function provideRealize(): array
    {
        return [
            [
                [1, 3, 3, 2],
                Collection::from([1, 3, 3, 2]),
            ],
        ];
    }

    public function provideReduce(): array
    {
        return [
            "Test sum" => [
                [6, 4, 5],
                fn ($sum, $number) => $sum + $number,
                0,
                false,
                15,
            ],
            [
                [1, 3, 3, 2],
                function ($temp, $item) {
                    $temp[] = $item;

                    return $temp;
                },
                ["a" => [1]],
                true,
                ["a" => [1], 0 => 1, 1 => 3, 2 => 3, 3 => 2],
            ],
            [
                [1, 3, 3, 2],
                fn ($temp, $item) => $temp + $item,
                0,
                false,
                9,
            ],
            [
                [1, 3, 3, 2],
                fn ($temp, $item, $key) => $temp + $key + $item,
                0,
                false,
                15,
            ],
            [
                [1, 3, 3, 2],
                fn (CollectionInterface $temp, $item) => $temp->append($item),
                new Collection([]),
                false,
                [1, 3, 3, 2],
            ],
        ];
    }

    public function provideReduceRight(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($temp, $e) => $temp . $e,
                0,
                "02331",
            ],
            [
                [1, 3, 3, 2],
                fn ($temp, $key, $item) => $temp + $key + $item,
                0,
                15,
            ],
        ];
    }

    public function provideReductions(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($tmp, $i) => $tmp + $i,
                0,
                [0, 1, 4, 7, 9],
            ],
        ];
    }

    public function provideReferenceKeyValue(): array
    {
        return [
            [
                ["a", "b"],
                [[0, "a"], [1, "b"]],
            ],
        ];
    }

    public function provideReject(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v == 3,
                [1, 3 => 2],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $k == 2 && $v == 3,
                [1, 3, 3 => 2],
            ],
        ];
    }

    public function provideRepeat(): array
    {
        return [
            [
                1,
                1,
                [1],
            ],
        ];
    }

    public function provideRepeat_infinite(): array
    {
        return [
            [
                1,
                2,
                [1, 1],
            ],
            [
                1, 3,
                [1, 1, 1],
            ],
        ];
    }

    public function provideReplace(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [3 => "a"],
                [1, "a", "a", 2],
            ],
        ];
    }

    public function provideReplaceByKeys(): array
    {
        return [
            [
                ["a" => 1, "b" => 2, "c" => 3],
                ["b" => 3],
                ["a" => 1, "b" => 3, "c" => 3],
            ],
        ];
    }

    public function provideReverse(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [
                    3 => 2,
                    2 => 3,
                    1 => 3,
                    0 => 1,
                ],
            ],
        ];
    }

    public function provideSecond(): array
    {
        return [
            [
                [1, 2],
                2,
            ],
        ];
    }

    public function provideSecond_fail(): array
    {
        return [
            [
                [1],
                ItemNotFound::class,
            ],
        ];
    }

    public function provideSerializeUnserialize(): array
    {
        return [
            [
                [1, 2, 3],
            ],
            [
                ["foo" => "bar", 0 => 1],
            ],
        ];
    }

    public function provideShuffle(): array
    {
        return [
            [
                [1, 3, 3, 2],
                9,
            ],
        ];
    }

    public function provideSize(): array
    {
        return [
            "Test array" => [
                [1, 2, 3],
                3,
            ],
            "Test iterator" => [
                new ArrayIterator([1, 2, 3, 4]),
                4,
            ],
            "Test countable" => [
                new class implements Countable, IteratorAggregate {
                    public function count(): int
                    {
                        return 1337;
                    }

                    public function getIterator(): Traversable
                    {
                        yield from [1, 2, 3];
                    }
                },
                1337,
            ],
        ];
    }

    public function provideSizeIs(): array
    {
        return [
            [
                [1, 2],
                2,
                true,
            ],
            [
                [1, 2],
                3,
                false,
            ],
            [
                [1, 2],
                0,
                false,
            ],
        ];
    }

    public function provideSizeIsBetween(): array
    {
        return [
            [
                [1, 2],
                1,
                3,
                true,
            ],
            [
                [1, 2],
                3,
                4,
                false,
            ],
            [
                [1, 2],
                0,
                0,
                false,
            ],
            [
                [1, 2],
                3,
                1,
                true,
            ],
        ];
    }

    public function provideSizeIsGreaterThan(): array
    {
        return [
            [
                [1, 2],
                2,
                false,
            ],
            [
                [1, 2],
                1,
                true,
            ],
            [
                [1, 2],
                0,
                true,
            ],
        ];
    }

    public function provideSizeIsLessThan(): array
    {
        return [
            [
                [1, 2],
                0,
                false,
            ],
            [
                [1, 2],
                2,
                false,
            ],
            [
                [1, 2],
                3,
                true,
            ],
        ];
    }

    public function provideSlice(): array
    {
        return [
            [
                [1, 2, 3, 4, 5],
                2,
                4,
                [2 => 3, 3 => 4],
            ],
            [
                [1, 2, 3, 4, 5],
                4,
                -1,
                [4 => 5],
            ],
        ];
    }

    public function provideSome(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v < -1,
                false,
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $v > 0 && $k < -1,
                false,
            ],
            [
                [1, 3, 3, 2],
                fn ($v) => $v < 2,
                true,
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $v > 0 && $k > 0,
                true,
            ],
        ];
    }

    public function provideSort(): array
    {
        return [
            [
                [1, 3, 2],
                fn ($a, $b) => compare($a, $b),
                [0 => 1, 1 => 3, 2 => 2],
            ],
            [
                [3, 1, 2],
                fn ($a, $b) => $a > $b ? 1 : -1,
                [1 => 1, 2 => 2, 0 => 3],
            ],
            [
                [3, 1, 2],
                fn ($v1, $v2, $k1, $k2) => $k1 < $k2 || $v1 == $v2 ? 1 : -1,
                [2 => 2, 1 => 1, 0 => 3],
            ],
        ];
    }

    public function provideSplitAt(): array
    {
        return [
            [
                [1, 3, 3, 2],
                2,
                [
                    [1, 3],
                    [2 => 3, 3 => 2],
                ],
            ],
        ];
    }

    public function provideSplitWith(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v < 3,
                [
                    [1],
                    [1 => 3, 2 => 3, 3 => 2],
                ],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $v < 2 && $k < 3,
                [
                    [1],
                    [1 => 3, 2 => 3, 3 => 2],
                ],
            ],
        ];
    }

    public function provideSum(): array
    {
        return [
            [
                [1, 2, 3, 4],
                10,
            ],
        ];
    }

    public function provideTake(): array
    {
        return [
            [
                [1, 3, 3, 2],
                2,
                [1, 3],
            ],
        ];
    }

    public function provideTakeNth(): array
    {
        return [
            [
                [1, 3, 3, 2],
                2,
                [1, 2 => 3],
            ],
        ];
    }

    public function provideTakeWhile(): array
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v < 3,
                [1],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => $k < 2 && $v < 3,
                [1],
            ],
        ];
    }

    public function provideToArray(): array
    {
        return [
            [
                function () {
                    yield "no key";
                    yield "with key" => "this value is overwritten by the same key";
                    yield "nested" => [
                        "y" => "z",
                    ];
                    yield "iterator is not converted" => new ArrayIterator(["foo"]);
                    yield "with key" => "x";
                },
                [
                    "no key",
                    "with key" => "x",
                    "nested" => [
                        "y" => "z",
                    ],
                    "iterator is not converted" => new ArrayIterator(["foo"]),
                ],
            ],
        ];
    }

    public function provideToString(): array
    {
        return [
            [
                [2, "a", 3, null],
                "2a3",
            ],
        ];
    }

    public function provideTransform(): array
    {
        return [
            [
                [1, 2, 3],
                fn (CollectionInterface $it) => $it->map("\\Nekman\\Collection\\increment"),
                [2, 3, 4],
            ],
        ];
    }

    public function provideTransform_fail(): array
    {
        return [
            [
                [1, 2, 3],
                fn (CollectionInterface $it) => $it->first(),
                InvalidReturnValue::class,
            ],
        ];
    }

    public function provideTranspose(): array
    {
        return [
            [
                [
                    new Collection(["a", "b", "c", "d"]),
                    new Collection(["apple", "box", "car"]),
                ],
                [
                    new Collection(["a", "apple"]),
                    new Collection(["b", "box"]),
                    new Collection(["c", "car"]),
                    new Collection(["d", null]),
                ],
            ],
            [
                [
                    new Collection([1, 2, 3]),
                    new Collection([4, 5, new Collection(["foo", "bar"])]),
                    new Collection([7, 8, 9]),
                ],
                [
                    new Collection([1, 4, 7]),
                    new Collection([2, 5, 8]),
                    new Collection([3, new Collection(["foo", "bar"]), 9]),
                ],
            ],
        ];
    }

    public function provideTranspose_fail(): array
    {
        return [
            [
                [
                    [1, 2, 3],
                    [4, 5, 6],
                    [7, 8, 9],
                ],
                InvalidArgument::class,
            ],
        ];
    }

    public function provideValues(): array
    {
        return [
            [
                ["a" => 1, "b" => 2],
                [1, 2],
            ],
        ];
    }

    public function provideZip(): array
    {
        return [
            [
                [1, 2, 3],
                [
                    ['a' => 1, 'b' => 2, 'c' => 4],
                ],
                [
                    [1, 'a' => 1],
                    [1 => 2, 'b' => 2],
                    [2 => 3, 'c' => 4],
                ],
            ],
            [
                [1, 2, 3],
                [
                    [4, 5, 6],
                    [7, 8, 9],
                ],
                [
                    [1, 4, 7],
                    [2, 5, 8],
                    [3, 6, 9],
                ],
            ],
            [
                [1, 2, 3],
                [
                    [4, 5]
                ],
                [
                    [1, 4],
                    [2, 5],
                ],
            ]
        ];
    }

    /** @dataProvider provideAppend */
    public function testAppend($input, $append, $key, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->append($append, $key)->toArray()
        );
    }

    /** @dataProvider provideAverage */
    public function testAverage($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->average());
    }

    /** @dataProvider provideCombine */
    public function testCombine($input, $combine, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->combine($combine)->toArray());
    }

    /** @dataProvider provideConcat */
    public function testConcat($input, $concat, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->concat(...$concat)->toArray());
    }

    /** @dataProvider provideContains */
    public function testContains($input, $contains, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->contains($contains));
    }

    /** @dataProvider provideCountBy */
    public function testCountBy($input, $countBy, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->countBy($countBy));
    }

    /** @dataProvider provideCycle */
    public function testCycle($input, $take, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->cycle()->take($take)->toArray(true));
    }

    /** @dataProvider provideDereferenceKeyValue */
    public function testDereferenceKeyValue($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->dereferenceKeyValue()->toArray(true));
    }

    /** @dataProvider provideDiff */
    public function testDiff($input, $diff, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->diff(...$diff)->toArray());
    }

    /** @dataProvider provideDistinct */
    public function testDistinct($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->distinct()->toArray());
    }

    /** @dataProvider provideDrop */
    public function testDrop($input, $nItems, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->drop($nItems)->toArray());
    }

    /** @dataProvider provideDropLast */
    public function testDropLast($input, $dropLast, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->dropLast($dropLast)->toArray());
    }

    /** @dataProvider provideDropWhile */
    public function testDropWhile($input, $dropWhile, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->dropWhile($dropWhile)->toArray());
    }

    /** @dataProvider provideDump */
    public function testDump($input, $maxItemsPerCollection, $maxDepth, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->dump($maxItemsPerCollection, $maxDepth));
    }

    /** @dataProvider provideEvery */
    public function testEvery($input, $every, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->every($every));
    }

    /** @dataProvider provideExcept */
    public function testExcept($input, $except, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->except($except)->toArray());
    }

    /** @dataProvider provideExtract */
    public function testExtract($input, $extract, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->extract($extract)->toArray());
    }

    /** @dataProvider provideFilter */
    public function testFilter($input, $filter, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->filter($filter)->toArray());
    }

    public function testFilter_falsy_values(): void
    {
        $input = [false, null, "", 0, 0.0, []];
        $this->assertTrue(Collection::from($input)->filter()->isEmpty());
    }

    /** @dataProvider provideFind */
    public function testFind($input, $find, $default, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->find($find, $default));
    }

    /** @dataProvider provideFirst */
    public function testFirst($input, $convertToIterable, $expect): void
    {
        $result = Collection::from($input)->first($convertToIterable);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expect, $result);
    }

    /** @dataProvider provideFirst_fail */
    public function testFirst_fail($input, $expected): void
    {
        $this->expectException($expected);
        Collection::from($input)->first();
    }

    /** @dataProvider provideFlatten */
    public function testFlatten($input, $depth, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->flatten($depth)->toArray(true)
        );
    }

    /** @dataProvider provideFlip */
    public function testFlip($input, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->flip()->toArray()
        );
    }

    /** @dataProvider provideFrequencies */
    public function testFrequencies($input, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->frequencies()->toArray()
        );
    }

    /** @dataProvider provideFromAndCreate */
    public function testFrom($input, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->toArray(true)
        );
    }

    /** @dataProvider provideFromAndCreate_fail */
    public function testFrom_fail($input, $expected): void
    {
        $this->expectException($expected);
        Collection::from($input)->toArray(true);
    }

    /** @dataProvider provideGet */
    public function testGet($input, $key, $convertToIterable, $expect): void
    {
        $result = Collection::from($input)->get($key, $convertToIterable);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expect, $result);
    }

    /** @dataProvider provideGetOrDefault */
    public function testGetOrDefault($input, $key, $default, $convertToIterable, $expect): void
    {
        $result = Collection::from($input)->getOrDefault($key, $default, $convertToIterable);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expect, $result);
    }

    /** @dataProvider provideGet_fail */
    public function testGet_fail($input, $key, $expect): void
    {
        $this->expectException($expect);
        Collection::from($input)->get($key);
    }

    /** @dataProvider provideGroupBy */
    public function testGroupBy($input, $groupBy, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->groupBy($groupBy)->toArrayRecursive()
        );
    }

    /** @dataProvider provideGroupByKey */
    public function testGroupByKey($input, $key, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->groupByKey($key)->toArray()
        );
    }

    /** @dataProvider provideHas */
    public function testHas($input, $has, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->has($has)
        );
    }

    /** @dataProvider provideIndexBy */
    public function testIndexBy($input, $indexBy, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->indexBy($indexBy)->toArray()
        );
    }

    /** @dataProvider provideInterleave */
    public function testInterleave($input, $interleave, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->interleave(...$interleave)->toArray(true));
    }

    /** @dataProvider provideInterpose */
    public function testInterpose($input, $interpose, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->interpose($interpose)->toArray(true));
    }

    /** @dataProvider provideIntersect */
    public function testIntersect($input, $intersect, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->intersect(...$intersect)->toArray(true)
        );
    }

    /** @dataProvider provideIsEmpty */
    public function testIsEmpty($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->isEmpty());
    }

    /** @dataProvider provideIsNotEmpty */
    public function testIsNotEmpty($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->isNotEmpty());
    }

    public function testIterate_infinite(): void
    {
        $this->assertEquals(
            [1, 2],
            Collection::iterate(1, fn ($i) => $i + 1)->take(2)->toArray()
        );
    }

    public function testIterate_not_infinite(): void
    {
        $this->assertEquals(
            [1, 2, 3, 4],
            Collection::iterate(1, fn ($i) => $i > 3 ? throw new NoMoreItems : $i + 1)->toArray()
        );
    }

    /** @dataProvider provideKeys */
    public function testKeys($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->keys()->toArray());
    }

    /** @dataProvider provideLast */
    public function testLast($input, $convertToIterable, $expect): void
    {
        $result = Collection::from($input)->last($convertToIterable);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expect, $result);
    }

    /** @dataProvider provideLast_fail */
    public function testLast_fail($input, $expected): void
    {
        $this->expectException($expected);
        Collection::from($input)->last();
    }

    /** @dataProvider provideMap */
    public function testMap($input, $map, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->map($map)->toArray(true));
    }

    /** @dataProvider provideMapcat */
    public function testMapcat($input, $mapcat, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->mapcat($mapcat)->toArray(true));
    }

    /** @dataProvider provideMax */
    public function testMax($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->max());
    }

    /** @dataProvider provideMin */
    public function testMin($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->min());
    }

    /** @dataProvider provideFromAndCreate */
    public function testNew($input, $expected): void
    {
        $this->assertEquals($expected, (new Collection($input))->toArray(true));
    }

    /** @dataProvider provideFromAndCreate_fail */
    public function testNew_fail($input, $expected): void
    {
        $this->expectException($expected);
        (new Collection($input))->toArray(true);
    }

    /** @dataProvider provideOnly */
    public function testOnly($input, $only, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->only($only)->toArray());
    }

    /** @dataProvider providePartition */
    public function testPartition($input, $nItems, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->partition($nItems)->toArrayRecursive()
        );
    }

    /** @dataProvider providePartitionBy */
    public function testPartitionBy($input, $partitionBy, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->partitionBy($partitionBy)->toArray());
    }

    /** @dataProvider providePrepend */
    public function testPrepend($input, $prepend, $key, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->prepend($prepend, $key)->toArray()
        );
    }

    /** @dataProvider providePrintDump */
    public function testPrintDump($input): void
    {
        $col = Collection::from($input);
        ob_start();
        $this->assertEquals($col, $col->printDump());
        $this->assertNotEmpty(ob_get_clean());
    }

    /** @dataProvider provideRange */
    public function testRange($start, $end, $take, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::range($start, $end)->take($take)->toArray()
        );
    }

    /** @dataProvider provideRealize */
    public function testRealize($input, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->realize()
        );
    }

    /** @dataProvider provideReduce */
    public function testReduce($input, $reduce, $startValue, $convertToIterable, $expected): void
    {
        $result = Collection::from($input)->reduce($reduce, $startValue, $convertToIterable);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expected, $result);
    }

    /** @dataProvider provideReduceRight */
    public function testReduceRight($input, $reduce, $startValue, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->reduceRight($reduce, $startValue)
        );
    }

    /** @dataProvider provideReductions */
    public function testReductions($input, $reductions, $startValue, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->reductions($reductions, $startValue)->toArray());
    }

    /** @dataProvider provideReferenceKeyValue */
    public function testReferenceKeyValue($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->referenceKeyValue()->toArray(true));
    }

    /** @dataProvider provideReject */
    public function testReject($input, $reject, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->reject($reject)->toArray());
    }

    /** @dataProvider provideRepeat */
    public function testRepeat($startValue, $nItems, $expect): void
    {
        $this->assertEquals($expect, Collection::repeat($startValue, $nItems)->toArray());
    }

    /** @dataProvider provideRepeat_infinite */
    public function testRepeat_infinite($startValue, $nItems, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::repeat($startValue)->take($nItems)->toArray()
        );
    }

    /** @dataProvider provideReplace */
    public function testReplace($input, $replace, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->replace($replace)->toArray());
    }

    /** @dataProvider provideReplaceByKeys */
    public function testReplaceByKeys($input, $replaceByKeys, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->replaceByKeys($replaceByKeys)->toArray());
    }

    /** @dataProvider provideReverse */
    public function testReverse($input, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->reverse()->toArray()
        );
    }

    /** @dataProvider provideSecond */
    public function testSecond($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->second());
    }

    /** @dataProvider provideSecond_fail */
    public function testSecond_fail($input, $expect): void
    {
        $this->expectException($expect);
        Collection::from($input)->second();
    }

    /** @dataProvider provideSerializeUnserialize */
    public function testSerializeUnserialize($input): void
    {
        $this->assertEquals($input, unserialize(serialize($input)));
    }

    /** @dataProvider provideShuffle */
    public function testShuffle($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->shuffle()->sum());
    }

    /** @dataProvider provideSize */
    public function testSize($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->size());
    }

    /** @dataProvider provideSizeIs */
    public function testSizeIs($input, $sizeIs, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->sizeIs($sizeIs));
    }

    /** @dataProvider provideSizeIsBetween */
    public function testSizeIsBetween($input, $from, $to, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->sizeIsBetween($from, $to));
    }

    /** @dataProvider provideSizeIsGreaterThan */
    public function testSizeIsGreaterThan($input, $greaterThan, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->sizeIsGreaterThan($greaterThan));
    }

    /** @dataProvider provideSizeIsLessThan */
    public function testSizeIsLessThan($input, $lessThan, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->sizeIsLessThan($lessThan));
    }

    /** @dataProvider provideSlice */
    public function testSlice($input, $from, $to, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->slice($from, $to)->toArray()
        );
    }

    /** @dataProvider provideSome */
    public function testSome($input, $some, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->some($some));
    }

    /** @dataProvider provideSort */
    public function testSort($input, $sort, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->sort($sort)->toArray()
        );
    }

    /** @dataProvider provideSplitAt */
    public function testSplitAt($input, $position, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->splitAt($position)->toArrayRecursive()
        );
    }

    /** @dataProvider provideSplitWith */
    public function testSplitWith($input, $contains, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->splitWith($contains)->toArrayRecursive()
        );
    }

    /** @dataProvider provideSum */
    public function testSum($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->sum());
    }

    /** @dataProvider provideTake */
    public function testTake($input, $take, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->take($take)->toArray());
    }

    /** @dataProvider provideTakeNth */
    public function testTakeNth($input, $step, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->takeNth($step)->toArray());
    }

    /** @dataProvider provideTakeWhile */
    public function testTakeWhile($input, $takeWhile, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->takeWhile($takeWhile)->toArray()
        );
    }

    /** @dataProvider provideToArray */
    public function testToArray($input, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::from($input)->toArray()
        );
    }

    /** @dataProvider provideToString */
    public function testToString($input, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->toString()
        );
    }

    /** @dataProvider provideTransform */
    public function testTransform($input, $transform, $expect): void
    {
        $this->assertEquals(
            $expect,
            Collection::from($input)->transform($transform)->toArray()
        );
    }

    /** @dataProvider provideTransform_fail */
    public function testTransform_fail($input, $transform, $expect): void
    {
        $this->expectException($expect);
        Collection::from($input)->transform($transform)->toArray();
    }

    /** @dataProvider provideTranspose */
    public function testTranspose($input, $expect): void
    {
        $this->markTestSkipped();

        $this->assertEquals(
            $expect,
            Collection::from($input)->transpose()->toArray()
        );
    }

    /** @dataProvider provideTranspose_fail */
    public function testTranspose_fail($input, $expect): void
    {
        $this->markTestSkipped();

        $this->expectException($expect);
        Collection::from($input)->transpose()->toArray();
    }

    /** @dataProvider provideValues */
    public function testValues($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->values()->toArray());
    }

    /** @dataProvider provideZip */
    public function testZip($input, $zip, $expect): void
    {
        $this->markTestSkipped();

        $this->assertEquals(
            $expect,
            Collection::from($input)->zip(...$zip)->toArrayRecursive()
        );
    }
}
