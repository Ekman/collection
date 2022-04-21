<?php

namespace Nekman\Collection\Tests\Legacy;

use ArrayIterator;
use DusanKasan\Knapsack\Tests\Helpers\Car;
use Nekman\Collection\Collection;
use Nekman\Collection\Exceptions\ItemNotFound;
use function DusanKasan\Knapsack\concat;

final class CollectionTest extends ObjectBehavior
{
    public function it_can_group_by(): void
    {
        $this->beConstructedWith([1, 2, 3, 4, 5]);

        $collection = $this->groupBy(function ($i) {
            return $i % 2;
        });

        $collection->get(0)->toArray()->shouldReturn([2, 4]);
        $collection->get(1)->toArray()->shouldReturn([1, 3, 5]);

        $collection = $this->groupBy(function ($k, $i) {
            return ($k + $i) % 3;
        });
        $collection->get(0)->toArray()->shouldReturn([2, 5]);
        $collection->get(1)->toArray()->shouldReturn([1, 4]);
        $collection->get(2)->toArray()->shouldReturn([3]);
    }

    public function it_can_group_by_key(): void
    {
        $this->beConstructedWith([
            'some' => 'thing',
            ['letter' => 'A', 'type' => 'caps'],
            ['letter' => 'a', 'type' => 'small'],
            ['letter' => 'B', 'type' => 'caps'],
            ['letter' => 'Z'],
        ]);

        $collection = $this->groupByKey('type');
        $collection->get('small')->toArray()->shouldReturn([
            ['letter' => 'a', 'type' => 'small'],
        ]);
        $collection->get('caps')->toArray()->shouldReturn([
            ['letter' => 'A', 'type' => 'caps'],
            ['letter' => 'B', 'type' => 'caps'],
        ]);

        $collection = $this->groupByKey('types');
        $collection->shouldThrow(new ItemNotFound)->during('get', ['caps']);
    }

    public function it_can_index_by(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->indexBy(function ($v) {
                return $v;
            })
            ->toArray()
            ->shouldReturn([1 => 1, 3 => 3, 2 => 2]);

        $this
            ->indexBy(function ($v, $k) {
                return $k . $v;
            })
            ->toArray()
            ->shouldReturn(['01' => 1, '13' => 3, '23' => 3, '32' => 2]);
    }

    public function it_can_interleave_elements(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->interleave(['a', 'b', 'c', 'd', 'e'])
            ->values()
            ->toArray()
            ->shouldReturn([1, 'a', 3, 'b', 3, 'c', 2, 'd', 'e']);
    }

    public function it_can_interpose(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->interpose('a')
            ->values()
            ->toArray()
            ->shouldReturn([1, 'a', 3, 'a', 3, 'a', 2]);
    }

    public function it_can_map_and_then_concatenate_a_collection(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->mapcat(function ($v) {
                return [[$v]];
            })
            ->values()
            ->toArray()
            ->shouldReturn([[1], [3], [3], [2]]);

        $this
            ->mapcat(function ($v, $k) {
                return [[$k + $v]];
            })
            ->values()
            ->toArray()
            ->shouldReturn([[1], [4], [5], [5]]);
    }

    public function it_can_partition(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $s1 = $this->partition(3, 2, [0, 1]);
        $s1->size()->shouldBe(2);
        $s1->first()->toArray()->shouldBe([1, 3, 3]);
        $s1->second()->toArray()->shouldBe([2 => 3, 3 => 2, 0 => 0]);

        $s2 = $this->partition(3, 2);
        $s2->size()->shouldBe(2);
        $s2->first()->toArray()->shouldBe([1, 3, 3]);
        $s2->second()->toArray()->shouldBe([2 => 3, 3 => 2]);

        $s3 = $this->partition(3);
        $s3->size()->shouldBe(2);
        $s3->first()->toArray()->shouldBe([1, 3, 3]);
        $s3->second()->toArray()->shouldBe([3 => 2]);

        $s4 = $this->partition(1, 3);
        $s4->size()->shouldBe(2);
        $s4->first()->toArray()->shouldBe([1,]);
        $s4->second()->toArray()->shouldBe([3 => 2]);
    }

    public function it_can_partition_by(): void
    {
        $this->beConstructedWith([1, 3, 3, 2]);

        $s1 = $this->partitionBy(function ($v) {
            return $v % 3 == 0;
        });
        $s1->size()->shouldBe(3);
        $s1->first()->toArray()->shouldBe([1]);
        $s1->second()->toArray()->shouldBe([1 => 3, 2 => 3]);
        $s1->values()->get(2)->toArray()->shouldBe([3 => 2]);

        $s2 = $this->partitionBy(function ($v, $k) {
            return $k - $v;
        });
        $s2->size()->shouldBe(4);
        $s2->first()->toArray()->shouldBe([1]);
        $s2->values()->get(1)->toArray()->shouldBe([1 => 3]);
        $s2->values()->get(2)->toArray()->shouldBe([2 => 3]);
        $s2->values()->get(3)->toArray()->shouldBe([3 => 2]);
    }

    public function it_can_prepend_item(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->prepend(1)
            ->values()
            ->toArray()
            ->shouldReturn([1, 1, 3, 3, 2]);
    }

    public function it_can_prepend_item_with_key(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->prepend(1, 'a')
            ->toArray()
            ->shouldReturn(['a' => 1, 0 => 1, 1 => 3, 2 => 3, 3 => 2]);
    }

    public function it_can_print_dump(): void
    {
        $this->beConstructedWith([1, [2], 3]);

        ob_start();
        $this->printDump()->shouldReturn($this);
        $this->printDump(2)->shouldReturn($this);
        $this->printDump(2, 2)->shouldReturn($this);
        ob_clean();
    }

    public function it_can_realize_the_collection(PlusOneAdder $adder): void
    {
        $adder->dynamicMethod(1)->willReturn(2);
        $adder->dynamicMethod(2)->willReturn(3);

        $this->beConstructedWith(function () use ($adder) {
            yield $adder->dynamicMethod(1);
            yield $adder->dynamicMethod(2);
        });

        $this->realize();
    }

    public function it_can_reduce_from_right(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->reduceRight(
                function ($temp, $e) {
                    return $temp . $e;
                },
                0
            )
            ->shouldReturn('02331');

        $this
            ->reduceRight(
                function ($temp, $key, $item) {
                    return $temp + $key + $item;
                },
                0
            )
            ->shouldReturn(15);

        $this
            ->reduceRight(
                function (Collection $temp, $item) {
                    return $temp->append($item);
                },
                new Collection([])
            )
            ->toArray()
            ->shouldReturn([2, 3, 3, 1]);
    }

    public function it_can_reject_elements_from_collection(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->reject(function ($v) {
                return $v == 3;
            })
            ->toArray()
            ->shouldReturn([1, 3 => 2]);

        $this
            ->reject(function ($v, $k) {
                return $k == 2 && $v == 3;
            })
            ->toArray()
            ->shouldReturn([1, 3, 3 => 2]);
    }

    public function it_can_repeat_items_of_collection_infinitely(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->cycle()
            ->take(8)
            ->values()
            ->toArray()
            ->shouldReturn([1, 3, 3, 2, 1, 3, 3, 2]);
    }

    public function it_can_replace_by_key(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->replaceByKeys(['b' => 3])->toArray()->shouldReturn(['a' => 1, 'b' => 3, 'c' => 3]);
    }

    public function it_can_replace_items_by_items_from_another_collection(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->replace([3 => 'a'])
            ->toArray()
            ->shouldReturn([1, 'a', 'a', 2]);
    }

    public function it_can_return_every_nth_item(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->takeNth(2)
            ->toArray()
            ->shouldReturn([1, 2 => 3]);
    }

    public function it_can_return_only_first_x_elements(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->take(2)
            ->toArray()
            ->shouldReturn([1, 3]);
    }

    public function it_can_return_values(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2]);
        $this->values()->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_reverse(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->reverse()
            ->toArray()
            ->shouldReturn([
                3 => 2,
                2 => 3,
                1 => 3,
                0 => 1,
            ]);
    }

    public function it_can_serialize_and_unserialize(): void
    {
        $original = Collection::from([1, 2, 3])->take(2);
        $this->beConstructedWith([1, 2, 3, 4]);
        $this->unserialize($original->serialize());
        $this->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_shuffle_itself(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->shuffle()
            ->reduce(
                function ($tmp, $i) {
                    return $tmp + $i;
                },
                0
            )
            ->shouldReturn(9);
    }

    public function it_can_skip_first_x_elements(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->drop(2)
            ->toArray()
            ->shouldReturn([2 => 3, 3 => 2]);
    }

    public function it_can_slice(): void
    {
        $this->beConstructedWith([1, 2, 3, 4, 5]);

        $this
            ->slice(2, 4)
            ->toArray()
            ->shouldReturn([2 => 3, 3 => 4]);

        $this
            ->slice(4)
            ->toArray()
            ->shouldReturn([4 => 5]);
    }

    public function it_can_split_the_collection_at_nth_item(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->splitAt(2)->size()->shouldBe(2);
        $this->splitAt(2)->first()->toArray()->shouldBeLike([1, 3]);
        $this->splitAt(2)->second()->toArray()->shouldBeLike([2 => 3, 3 => 2]);
    }

    public function it_can_split_the_collection_with_predicament(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $s1 = $this->splitWith(function ($v) {
            return $v < 3;
        });

        $s1->size()->shouldBe(2);
        $s1->first()->toArray()->shouldBe([1]);
        $s1->second()->toArray()->shouldBe([1 => 3, 2 => 3, 3 => 2]);

        $s2 = $this->splitWith(function ($v, $k) {
            return $v < 2 && $k < 3;
        });

        $s2->size()->shouldBe(2);
        $s2->first()->toArray()->shouldBe([1]);
        $s2->second()->toArray()->shouldBe([1 => 3, 2 => 3, 3 => 2]);
    }

    public function it_can_sum_the_collection(): void
    {
        $this->beConstructedWith([1, 2, 3, 4]);
        $this->sum()->shouldReturn(10);
        $this->append(1.5)->sum()->shouldReturn(11.5);
    }

    public function it_can_take_items_while_predicament_is_true(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->takeWhile(function ($v) {
                return $v < 3;
            })
            ->toArray()
            ->shouldReturn([1]);

        $this
            ->takeWhile(function ($v, $k) {
                return $k < 2 && $v < 3;
            })
            ->toArray()
            ->shouldReturn([1]);
    }

    public function it_can_transpose_arrays_of_different_lengths(): void
    {
        $this->beConstructedWith([
            new Collection(['a', 'b', 'c', 'd']),
            new Collection(['apple', 'box', 'car']),
        ]);

        $this->transpose()->toArray()->shouldBeLike([
            new Collection(['a', 'apple']),
            new Collection(['b', 'box']),
            new Collection(['c', 'car']),
            new Collection(['d', null]),
        ]);
    }

    public function it_can_transpose_collections_of_collections(): void
    {
        $this->beConstructedWith([
            new Collection([1, 2, 3]),
            new Collection([4, 5, new Collection(['foo', 'bar'])]),
            new Collection([7, 8, 9]),
        ]);

        $this->transpose()->toArray()->shouldBeLike([
            new Collection([1, 4, 7]),
            new Collection([2, 5, 8]),
            new Collection([3, new Collection(['foo', 'bar']), 9]),
        ]);
    }

    public function it_can_use_callable_as_transformer(): void
    {
        $this->beConstructedWith([1, 2, 3]);
        $this
            ->transform(function (Collection $collection) {
                return $collection->map('\DusanKasan\Knapsack\increment');
            })
            ->toArray()
            ->shouldReturn([2, 3, 4]);

        $this
            ->shouldThrow(InvalidReturnValue::class)
            ->during(
                'transform',
                [
                    function (Collection $collection) {
                        return $collection->first();
                    },
                ]
            );
    }

    public function it_can_use_the_utility_methods(): void
    {
        $this->beConstructedWith([1, 3, 2]);

        $this
            ->sort('\DusanKasan\Knapsack\compare')
            ->values()
            ->toArray()
            ->shouldReturn([1, 2, 3]);

        $this
            ->map('\DusanKasan\Knapsack\compare')
            ->toArray()
            ->shouldReturn([1, 1, 0]);

        $this
            ->map('\DusanKasan\Knapsack\decrement')
            ->toArray()
            ->shouldReturn([0, 2, 1]);
    }

    public function it_can_zip(): void
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->zip(['a' => 1, 'b' => 2, 'c' => 4])
            ->map('\DusanKasan\Knapsack\toArray')
            ->toArray()
            ->shouldReturn([[1, 'a' => 1], [1 => 2, 'b' => 2], [2 => 3, 'c' => 4]]);

        $this->zip([4, 5, 6], [7, 8, 9])
            ->map('\DusanKasan\Knapsack\values')
            ->map('\DusanKasan\Knapsack\toArray')
            ->toArray()
            ->shouldReturn([[1, 4, 7], [2, 5, 8], [3, 6, 9]]);

        $this->zip([4, 5])
            ->map('\DusanKasan\Knapsack\values')
            ->map('\DusanKasan\Knapsack\toArray')
            ->toArray()
            ->shouldReturn([[1, 4], [2, 5]]);
    }

    public function it_filters_by_keys(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->only(['a', 'b'])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
        $this->only(['a', 'b', 'x'])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
    }

    public function it_should_throw_an_invalid_argument_if_collection_items_are_not_collection(): void
    {
        $this->beConstructedWith([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $this->shouldThrow(InvalidArgument::class)->during('transpose');
    }

    public function it_throws_when_trying_to_get_non_existent_second_item(): void
    {
        $this->beConstructedWith([1]);
        $this->shouldThrow(ItemNotFound::class)->during('second');
    }

    public function it_will_return_null_when_max_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->max()->shouldReturn(null);
    }

    public function it_will_return_null_when_min_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->min()->shouldReturn(null);
    }

    public function it_will_return_zero_when_average_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->average()->shouldReturn(0);
    }

    public function it_will_throw_when_trying_to_get_first_item_of_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(ItemNotFound::class)->during('first');
    }

    public function it_will_throw_when_trying_to_get_last_item_of_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(ItemNotFound::class)->during('last');
    }
}
