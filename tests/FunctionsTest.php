<?php

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
    /** @dataProvider provideFrom */
    public function testFrom($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->toArray());
    }

    public function provideFrom()
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
            "Test iterable function" => [
                fn () => [3, 4, 5],
                [3, 4, 5]
            ]
        ];
    }

    /** @dataProvider provideAppend */
    public function testAppend($input, $append, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->append($append)->values()->toArray());
    }

    public function provideAppend()
    {
        return [
            [
                [1, 3, 3, 2],
                1,
                [1, 3, 3, 2, 1],
            ]
        ];
    }

    /** @dataProvider provideAverage */
    public function testAverage($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->average());
    }

    public function provideAverage()
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

    /** @dataProvider provideCombine */
    public function testCombine($input, $combine, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->combine($combine)->toArray());
    }

    public function provideCombine()
    {
        return [
            [
                ["a", "b"],
                [1, 2],
                ["a" => 1, "b" => 2]
            ]
        ];
    }

    /** @dataProvider provideConcat */
    public function testConcat($input, $concat, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->concat(...$concat)->toArray());
    }

    public function provideConcat()
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

    /** @dataProvider provideMap */
    public function testMap($input, $map, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->map($map)->toArray());
    }

    public function provideMap()
    {
        return [
            [
                [1, 2, 3],
                fn ($number) => $number * 2,
                [2, 4, 6]
            ]
        ];
    }

    /** @dataProvider provideSize */
    public function testSize($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->size());
    }

    public function provideSize()
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

    /** @dataProvider provideReduce */
    public function testReduce($input, $reduce, $initial, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->reduce($reduce, $initial));
    }

    public function provideReduce()
    {
        return [
            "Test sum" => [
                [6, 4, 5],
                fn ($sum, $number) => $sum + $number,
                0,
                15,
            ]
        ];
    }

    /** @dataProvider provideReverse */
    public function testReverse($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->reverse()->toArray());
    }

    public function provideReverse()
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

    /** @dataProvider provideSort */
    public function testSort($input, $sort, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->sort($sort)->values()->toArray());
    }

    public function provideSort()
    {
        return [
            [
                [1, 3, 2],
                "\Nekman\Collection\compare",
                [1, 2, 3],
            ]
        ];
    }

    /** @dataProvider provideDereferenceKeyValue */
    public function testDereferenceKeyValue($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->dereferenceKeyValue()->toArray());
    }

    public function provideDereferenceKeyValue()
    {
        return [
            [
                [[0, "a"], [1, "b"]],
                ["a", "b"],
            ]
        ];
    }

    /** @dataProvider provideReferenceKeyValue */
    public function testReferenceKeyValue($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->referenceKeyValue()->toArray());
    }

    public function provideReferenceKeyValue()
    {
        return [
            [
                ["a", "b"],
                [[0, "a"], [1, "b"]],
            ]
        ];
    }
}
