<?php

namespace Nekman\Collection\Tests;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Nekman\Collection\Collection;
use Nekman\Collection\Exceptions\InvalidArgument;
use Nekman\Collection\Exceptions\ItemNotFound;
use Nekman\Collection\Exceptions\NoMoreItems;
use Nekman\Collection\Tests\Helpers\Car;
use PHPUnit\Framework\TestCase;
use Traversable;
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
                'a',
                [1, 3, 3, 2, 'a' => 1],
            ]
        ];
    }

    public function provideAverage(): array
    {
        return [
            "Test int" => [
                [1, 2, 3],
                2
            ],
            "Test float" => [
                [6.4, 3.2, 8.9],
                6.166666666666667
            ]
        ];
    }

    public function provideCombine(): array
    {
        return [
            [
                ["a", "b"],
                [1, 2],
                ["a" => 1, "b" => 2]
            ],
            [
                ["a", "b"],
                [1],
                ["a" => 1],
            ],
            [
                ["a", "b"],
                [1, 2, 3],
                ["a" => 1, "b" => 2]
            ]
        ];
    }

    public function provideConcat(): array
    {
        return [
            [
                [1, 3, 3, 2],
                [
                    [4, 5]
                ],
                [4, 5, 3, 2]
            ]
        ];
    }

    public function provideContains()
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
            ]
        ];
    }

    public function provideCountBy()
    {
        return [
            [
                [1, 3, 3, 2],
                fn ($v) => $v % 2 == 0 ? "even" : "odd",
                ["odd" => 3, "even" => 1],
            ],
            [
                [1, 3, 3, 2],
                fn ($v, $k) => ($k + $v) % 2 == 0 ? 'even' : 'odd',
                ["odd" => 3, "even" => 1],
            ],
        ];
    }

    public function provideDereferenceKeyValue(): array
    {
        return [
            [
                [[0, "a"], [1, "b"]],
                ["a", "b"],
            ]
        ];
    }

    public function provideDiff()
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

    public function provideDistinct()
    {
        return [
            [
                [1, 3, 3, 2],
                [1, 3, 3 => 2],
            ]
        ];
    }

    public function provideDropLast()
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

    public function provideDropWhile()
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

    public function provideDump()
    {
        return [
            [
                [
                    [
                        [1, [2], 3],
                        ['a' => 'b'],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(['a', 'b', 'c']),
                    true,
                    new Car('sedan', 5),
                    iterable_concat([1], [1]),
                ],
                null,
                null,
                [
                    [
                        [1, [2], 3],
                        ['a' => 'b'],
                        [1, 2, 3],
                    ],
                    [1, 2, 3],
                    ['a', 'b', 'c'],
                    true,
                    [
                        'DusanKasan\Knapsack\Tests\Helpers\Car' => [
                            'numberOfSeats' => 5,
                        ],

                    ],
                    [1, '0//1' => 1],
                ]
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ['a' => 'b'],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(['a', 'b', 'c']),
                    true,
                    new Car('sedan', 5),
                    iterable_concat([1], [1]),
                ],
                2,
                null,
                [
                    [
                        [1, [2], '>>>'],
                        ['a' => 'b'],
                        '>>>',
                    ],
                    [1, 2, '>>>'],
                    '>>>',
                ]
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ['a' => 'b'],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(['a', 'b', 'c']),
                    true,
                    new Car('sedan', 5),
                    iterable_concat([1], [1]),
                ],
                null,
                3,
                [
                    [
                        [1, '^^^', 3],
                        ['a' => 'b'],
                        [1, 2, 3],
                    ],
                    [1, 2, 3],
                    ['a', 'b', 'c'],
                    true,
                    [
                        'DusanKasan\Knapsack\Tests\Helpers\Car' => [
                            'numberOfSeats' => 5,
                        ],
                    ],
                    [1, '0//1' => 1],
                ]
            ],
            [
                [
                    [
                        [1, [2], 3],
                        ['a' => 'b'],
                        new ArrayIterator([1, 2, 3]),
                    ],
                    [1, 2, 3],
                    new ArrayIterator(['a', 'b', 'c']),
                    true,
                    new Car('sedan', 5),
                    iterable_concat([1], [1]),
                ],
                2,
                3,
                [
                    [
                        [1, '^^^', '>>>'],
                        ['a' => 'b'],
                        '>>>',
                    ],
                    [1, 2, '>>>'],
                    '>>>',
                ]
            ],
        ];
    }

    public function provideEvery()
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

    public function provideExcept()
    {
        return [
            [
                ['a' => 1, 'b' => 2],
                ["a", "b"],
                [],
            ],
        ];
    }

    public function provideExtract()
    {
        return [
            [
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
                '',
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
            ],
            [
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
                "a.b",
                [1, 2],
            ],
            [
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
                "*.b",
                [1, 2, 3, 4, 5]
            ],
            [
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
                '\*.b',
                [3]
            ],
            [
                [
                    [
                        'a' => [
                            'b' => 1,
                        ],
                    ],
                    [
                        'a' => [
                            'b' => 2,
                        ],
                    ],
                    [
                        '*' => [
                            'b' => 3,
                        ],
                    ],
                    [
                        '.' => [
                            'b' => 4,
                        ],
                        'c' => [
                            'b' => 5,
                        ],
                        [
                            'a',
                        ],
                    ],
                ],
                '\..b',
                [4]
            ]
        ];
    }

    public function provideFilter()
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

    public function provideFind()
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

    public function provideFirst()
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

    public function provideFlatten()
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
            ]
        ];
    }

    public function provideFlip()
    {
        return [
            [
                ['a' => 1, 'b' => 2],
                [1 => 'a', 2 => 'b'],
            ],
        ];
    }

    public function provideFrequencies()
    {
        return [
            [
                [1, 3, 3, 2],
                [1 => 1, 3 => 2, 2 => 1],
            ]
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
                [1, 2]
            ],
            "Test generator function, return iterable" => [
                fn () => new ArrayIterator([1, 2]),
                [1, 2]
            ],
            "Test generator function, return collection" => [
                fn () => Collection::from([1, 2]),
                [1, 2]
            ],
            "Test iterable function" => [
                fn () => [3, 4, 5],
                [3, 4, 5]
            ],
            "Test collection" => [
                Collection::from(["foo", "bar"]),
                ["foo", "bar"],
            ]
        ];
    }

    public function provideFromAndCreate_fail(): array
    {
        return [
            [
                1,
                InvalidArgument::class,
            ],
        ];
    }

    public function provideGet()
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
                [[2]],
            ],
            [
                [1, [2], 3],
                1,
                false,
                [2],
            ],
        ];
    }

    public function provideGetOrDefault()
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
                [[2]],
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

    public function provideGet_fail()
    {
        return [
            [
                [1, 3, 3, 2],
                5,
                ItemNotFound::class,
            ],
        ];
    }

    public function provideHas()
    {
        return [
            [
                ['a' => 1, 'b' => 2],
                'a',
                true,
            ],
            [
                ['a' => 1, 'b' => 2],
                'x',
                true,
            ]
        ];
    }

    public function provideIsEmpty()
    {
        return [
            [
                [],
                true,
            ],
        ];
    }

    public function provideIsNotEmpty()
    {
        return [
            [
                [1, 3, 3, 2],
                true,
            ],
        ];
    }

    public function provideKeys()
    {
        return [
            [
                [1, 3, 3, 2],
                [0, 1, 2, 3],
            ],
        ];
    }

    public function provideLast()
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

    public function provideMap(): array
    {
        return [
            [
                [1, 2, 3],
                fn ($number) => $number * 2,
                [2, 4, 6]
            ]
        ];
    }

    public function provideMax()
    {
        return [
            [
                [1, 3, 3, 2],
                3,
            ],
        ];
    }

    public function provideMin()
    {
        return [
            [
                [1, 3, 3, 2],
                1,
            ],
        ];
    }

    public function provideRange()
    {
        return [
            [
                5,
                6,
                null,
                4,
                [5, 6]
            ],
            [
                5,
                null,
                null,
                2,
                [5, 6]
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
                ['a' => [1]],
                true,
                [[1], 1, 3, 3, 2],
            ],
            [
                [1, 3, 3, 2],
                function ($temp, $item) {
                    return $temp + $item;
                },
                0,
                false,
                9
            ],
            [
                [1, 3, 3, 2],
                function ($temp, $item, $key) {
                    return $temp + $key + $item;
                },
                0,
                false,
                15
            ],
            [
                [1, 3, 3, 2],
                function (Collection $temp, $item) {
                    return $temp->append($item);
                },
                new Collection([]),
                false,
                [1, 3, 3, 2],
            ]
        ];
    }

    public function provideReductions()
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
            ]
        ];
    }

    public function provideRepeat()
    {
        return [
            [
                1,
                1,
                [1],
            ],
        ];
    }

    public function provideRepeat_infinite()
    {
        return [
            [
                1,
                2,
                [1, 1]
            ],
            [
                1, 3,
                [1, 1, 1]
            ]
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
                ]
            ]
        ];
    }

    public function provideSecond()
    {
        return [
            [
                [1, 2],
                2,
            ],
        ];
    }

    public function provideSize(): array
    {
        return [
            "Test array" => [
                [1, 2, 3],
                3
            ],
            "Test iterator" => [
                new ArrayIterator([1, 2, 3, 4]),
                4
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
                1337
            ]
        ];
    }

    public function provideSizeIs()
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

    public function provideSizeIsBetween()
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

    public function provideSizeIsGreaterThan()
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

    public function provideSizeIsLessThan()
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

    public function provideSome()
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
                "\\Nekman\\Collection\\compare",
                [1, 2, 3],
            ],
            [
                [3, 1, 2],
                fn ($a, $b) => $a > $b,
                [1 => 1, 2 => 2, 0 => 3],
            ],
            [
                [3, 1, 2],
                function ($v1, $v2, $k1, $k2) {
                    return $k1 < $k2 || $v1 == $v2;
                },
                [2 => 2, 1 => 1, 0 => 3],
            ]
        ];
    }

    public function provideToArray()
    {
        return [
            [
                function () {
                    yield 'no key';
                    yield 'with key' => 'this value is overwritten by the same key';
                    yield 'nested' => [
                        'y' => 'z',
                    ];
                    yield 'iterator is not converted' => new ArrayIterator(["foo"]);
                    yield 'with key' => 'x';
                },
                [
                    'no key',
                    'with key' => 'x',
                    'nested' => [
                        'y' => 'z',
                    ],
                    'iterator is not converted' => new ArrayIterator(["foo"]),
                ],
            ]
        ];
    }

    public function provideToString()
    {
        return [
            [
                [2, "a", 3, null],
                "2a3",
            ],
        ];
    }

    /** @dataProvider provideAppend */
    public function testAppend($input, $append, $key, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->append($append, $key)->toArray(true));
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
        $input = [false, null, '', 0, 0.0, []];
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

    /** @dataProvider provideFlatten */
    public function testFlatten($input, $depth, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->flatten($depth)->toArray());
    }

    /** @dataProvider provideFlip */
    public function testFlip($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->flip()->toArray());
    }

    /** @dataProvider provideFrequencies */
    public function testFrequencies($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->frequencies()->toArray());
    }

    /** @dataProvider provideFromAndCreate */
    public function testFrom($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->toArray(true));
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

    /** @dataProvider provideHas */
    public function testHas($input, $has, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->has($has));
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

    /** @dataProvider provideMap */
    public function testMap($input, $map, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->map($map)->toArray(true));
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

    /** @dataProvider provideRange */
    public function testRange($input, $start, $end, $step, $take, $expect): void
    {
        $this->assertEquals($expect, Collection::range($start, $end, $step)->take($take)->toArray());
    }

    /** @dataProvider provideReduce */
    public function testReduce($input, $reduce, $initial, $convertToCollection, $expected): void
    {
        $result = Collection::from($input)->reduce($reduce, $initial, $convertToCollection);

        if (is_iterable($result)) {
            $result = iterable_to_array($result);
        }

        $this->assertEquals($expected, $result);
    }

    /** @dataProvider provideReductions */
    public function testReductions($input, $reductions, $initial, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->reductions($reductions, $initial)->toArray());
    }

    /** @dataProvider provideReferenceKeyValue */
    public function testReferenceKeyValue($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->referenceKeyValue()->toArray(true));
    }

    /** @dataProvider provideRepeat */
    public function testRepeat($initial, $nItems, $expect): void
    {
        $this->assertEquals($expect, Collection::repeat($initial, $nItems)->toArray());
    }

    /** @dataProvider provideRepeat_infinite */
    public function testRepeat_infinite($initial, $nItems, $expected): void
    {
        $this->assertEquals(
            $expected,
            Collection::repeat($initial)->take($nItems)->toArray()
        );
    }

    /** @dataProvider provideReverse */
    public function testReverse($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->reverse()->toArray());
    }

    /** @dataProvider provideSecond */
    public function testSecond($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->second());
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

    /** @dataProvider provideSome */
    public function testSome($input, $some, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->some($some));
    }

    /** @dataProvider provideSort */
    public function testSort($input, $sort, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->sort($sort)->toArray(true));
    }

    /** @dataProvider provideToArray */
    public function testToArray($input, $expected): void
    {
        $this->assertEquals($expected, Collection::from($input)->toArray());
    }

    /** @dataProvider provideToString */
    public function testToString($input, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->toString());
    }

    /** @dataProvider provideIntersect */
    public function testIntersect($input, $intersect, $expect): void
    {
        $this->assertEquals($expect, Collection::from($input)->intersect(...$intersect)->toArray());
    }

    public function provideIntersect()
    {
        return [
            [
                [1, 2, 3],
                [
                    [1, 2]
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
}
