<?php

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{
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

    public function provideDereferenceKeyValue()
    {
        return [
            [
                [[0, "a"], [1, "b"]],
                ["a", "b"],
            ]
        ];
    }

    public function provideFromAndCreate()
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
                fn() => yield from [1, 2],
                [1, 2]
            ],
            "Test iterable function" => [
                fn() => [3, 4, 5],
                [3, 4, 5]
            ],
            "Test collection" => [
                Collection::from(["foo", "bar"]),
                ["foo", "bar"],
            ]
        ];
    }

    public function provideMap()
    {
        return [
            [
                [1, 2, 3],
                fn($number) => $number * 2,
                [2, 4, 6]
            ]
        ];
    }

    public function provideReduce()
    {
        return [
            "Test sum" => [
                [6, 4, 5],
                fn($sum, $number) => $sum + $number,
                0,
                15,
            ]
        ];
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

    /** @dataProvider provideAppend */
    public function testAppend($input, $append, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->append($append)->toArray(true));
    }

    /** @dataProvider provideAverage */
    public function testAverage($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->average());
    }

    /** @dataProvider provideCombine */
    public function testCombine($input, $combine, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->combine($combine)->toArray());
    }

    /** @dataProvider provideConcat */
    public function testConcat($input, $concat, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->concat(...$concat)->toArray());
    }

    /** @dataProvider provideDereferenceKeyValue */
    public function testDereferenceKeyValue($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->dereferenceKeyValue()->toArray(true));
    }

    /** @dataProvider provideFromAndCreate */
    public function testFrom($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->toArray(true));
    }

    /** @dataProvider provideMap */
    public function testMap($input, $map, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->map($map)->toArray(true));
    }

    /** @dataProvider provideFromAndCreate */
    public function testNew($input, $expected)
    {
        $this->assertEquals($expected, (new Collection($input))->toArray(true));
    }

    /** @dataProvider provideReduce */
    public function testReduce($input, $reduce, $initial, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->reduce($reduce, $initial));
    }

    /** @dataProvider provideReferenceKeyValue */
    public function testReferenceKeyValue($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->referenceKeyValue()->toArray(true));
    }

    /** @dataProvider provideReverse */
    public function testReverse($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->reverse()->toArray());
    }

    /** @dataProvider provideSize */
    public function testSize($input, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->size());
    }

    /** @dataProvider provideSort */
    public function testSort($input, $sort, $expected)
    {
        $this->assertEquals($expected, Collection::from($input)->sort($sort)->toArray(true));
    }
}
