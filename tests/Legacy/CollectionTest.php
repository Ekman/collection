<?php

namespace Nekman\Collection\Tests\Legacy;

use Nekman\Collection\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function it_can_be_instantiated_from_iterator(): void
    {
        $iterator = new ArrayIterator([1, 2]);
        $this->beConstructedWith($iterator);
        $this->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_be_instantiated_from_iterator_aggregate(IteratorAggregate $iteratorAggregate): void
    {
        $iterator = new ArrayIterator([1, 2]);
        $iteratorAggregate->getIterator()->willReturn($iterator);
        $this->beConstructedWith($iteratorAggregate);
        $this->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_be_instantiated_from_array(): void
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->toArray()->shouldReturn([1, 2, 3]);
    }

    public function it_will_throw_when_passed_something_other_than_array_or_traversable(): void
    {
        $this->beConstructedWith(1);
        $this->shouldThrow(InvalidArgument::class)->duringInstantiation();
    }

    public function it_can_be_instantiated_from_callable_returning_an_array(): void
    {
        $this->beConstructedWith(function () {
            return [1, 2, 3];
        });
        $this->toArray()->shouldReturn([1, 2, 3]);
    }

    public function it_can_be_instantiated_from_callable_returning_an_iterator(): void
    {
        $this->beConstructedWith(function () {
            return new ArrayIterator([1, 2, 3]);
        });
        $this->toArray()->shouldReturn([1, 2, 3]);
    }

    public function it_can_be_instantiated_from_callable_returning_a_generator(): void
    {
        $this->beConstructedWith(function () {
            foreach ([1, 2, 3] as $value) {
                yield $value;
            }
        });
        $this->toArray()->shouldReturn([1, 2, 3]);
    }

    public function it_will_throw_when_passed_callable_will_return_something_other_than_array_or_traversable(): void
    {
        $this->beConstructedWith(function () {
            return 1;
        });
        $this->shouldThrow(InvalidReturnValue::class)->duringInstantiation();
    }

    public function it_can_be_created_statically(): void
    {
        $this->beConstructedThrough('from', [[1, 2]]);
        $this->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_be_created_to_iterate_over_function_infinitely(): void
    {
        $this->beConstructedThrough('iterate', [1, function ($i) {
            return $i + 1;
        }]);
        $this->take(2)->toArray()->shouldReturn([1, 2]);
    }

    public function it_can_be_created_to_iterate_over_function_non_infinitely(): void
    {
        $this->beConstructedThrough(
            'iterate',
            [
                1,
                function ($i) {
                    if ($i > 3) {
                        throw new NoMoreItems;
                    }

                    return $i + 1;
                },
            ]
        );
        $this->toArray()->shouldReturn([1, 2, 3, 4]);
    }

    public function it_can_be_created_to_repeat_a_value_infinite_times(): void
    {
        $this->beConstructedThrough('repeat', [1]);
        $this->take(2)->toArray()->shouldReturn([1, 1]);
    }

    public function it_can_convert_to_array(): void
    {
        $iterator = new \ArrayIterator([
            'foo',
        ]);

        $this->beConstructedWith(function () use ($iterator) {
            yield 'no key';
            yield 'with key' => 'this value is overwritten by the same key';
            yield 'nested' => [
                'y' => 'z',
            ];
            yield 'iterator is not converted' => $iterator;
            yield 'with key' => 'x';
        });

        $this
            ->toArray()
            ->shouldReturn([
                'no key',
                'with key' => 'x',
                'nested' => [
                    'y' => 'z',
                ],
                'iterator is not converted' => $iterator,
            ]);
    }

    public function it_can_filter(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->filter(function ($item) {
                return $item > 2;
            })
            ->toArray()
            ->shouldReturn([1 => 3, 2 => 3]);

        $this
            ->filter(function ($item, $key) {
                return $key > 2 && $item < 3;
            })
            ->toArray()
            ->shouldReturn([3 => 2]);
    }

    public function it_can_filter_falsy_values(): void
    {
        $this->beConstructedWith([false, null, '', 0, 0.0, []]);

        $this->filter()->isEmpty()->shouldReturn(true);
    }

    public function it_can_distinct(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->distinct()
            ->toArray()
            ->shouldReturn([1, 3, 3 => 2]);
    }

    public function it_can_concat(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $collection = $this->concat([4, 5]);
        $collection->toArray()->shouldReturn([4, 5, 3, 2]);
        $collection->size()->shouldReturn(6);
    }

    public function it_can_map(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->map(function ($item) {
                return $item + 1;
            })
            ->toArray()
            ->shouldReturn([2, 4, 4, 3]);
    }

    public function it_can_reduce(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->reduce(
                function ($temp, $item) {
                    $temp[] = $item;

                    return $temp;
                },
                ['a' => [1]],
                true
            )
            ->values()
            ->toArray()
            ->shouldReturn([[1], 1, 3, 3, 2]);

        $this
            ->reduce(
                function ($temp, $item) {
                    return $temp + $item;
                },
                0
            )
            ->shouldReturn(9);

        $this
            ->reduce(
                function ($temp, $item, $key) {
                    return $temp + $key + $item;
                },
                0
            )
            ->shouldReturn(15);

        $this
            ->reduce(
                function (Collection $temp, $item) {
                    return $temp->append($item);
                },
                new Collection([])
            )
            ->toArray()
            ->shouldReturn([1, 3, 3, 2]);
    }

    public function it_can_flatten(): void
    {
        $this->beConstructedWith([1, [2, [3]]]);
        $this->flatten()->values()->toArray()->shouldReturn([1, 2, 3]);
        $this->flatten(1)->values()->toArray()->shouldReturn([1, 2, [3]]);
    }

    public function it_can_sort(): void
    {
        $this->beConstructedWith([3, 1, 2]);

        $this
            ->sort(function ($a, $b) {
                return $a > $b;
            })
            ->toArray()
            ->shouldReturn([1 => 1, 2 => 2, 0 => 3]);

        $this
            ->sort(function ($v1, $v2, $k1, $k2) {
                return $k1 < $k2 || $v1 == $v2;
            })
            ->toArray()
            ->shouldReturn([2 => 2, 1 => 1, 0 => 3]);
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

    public function it_can_execute_callback_for_each_item(DOMXPath $a): void
    {
        $a->query('asd')->shouldBeCalled();
        $this->beConstructedWith([$a]);

        $this
            ->each(function (DOMXPath $i) {
                $i->query('asd');
            })
            ->toArray()
            ->shouldReturn([$a]);
    }

    public function it_can_get_size(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);
        $this->size()->shouldReturn(4);
    }

    public function it_can_get_item_by_key(): void
    {
        $this->beConstructedWith([1, [2], 3]);
        $this->get(0)->shouldReturn(1);
        $this->get(1, true)->first()->shouldReturn(2);
        $this->get(1)->shouldReturn([2]);
        $this->shouldThrow(new ItemNotFound)->during('get', [5]);
    }

    public function it_can_get_item_by_key_or_return_default(): void
    {
        $this->beConstructedWith([1, [2], 3]);
        $this->getOrDefault(0)->shouldReturn(1);
        $this->getOrDefault(1, null, true)->first()->shouldReturn(2);
        $this->getOrDefault(1, null)->shouldReturn([2]);
        $this->getOrDefault(5)->shouldReturn(null);
        $this->getOrDefault(5, 'not found')->shouldReturn('not found');
    }

    public function it_can_find(): void
    {
        $this->beConstructedWith([1, 3, 3, 2, [5]]);

        $this
            ->find(function ($v) {
                return $v < 3;
            })
            ->shouldReturn(1);

        $this
            ->find(function ($v, $k) {
                return $v < 3 && $k > 1;
            })
            ->shouldReturn(2);

        $this
            ->find(function ($v) {
                return $v < 0;
            })
            ->shouldReturn(null);

        $this
            ->find(
                function ($v) {
                    return $v < 0;
                },
                'not found'
            )
            ->shouldReturn('not found');

        $this->find('\DusanKasan\Knapsack\isCollection', null, true)->first()->shouldReturn(5);
        $this->find('\DusanKasan\Knapsack\isCollection')->shouldReturn([5]);
    }

    public function it_can_count_by(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->countBy(function ($v) {
                return $v % 2 == 0 ? 'even' : 'odd';
            })
            ->toArray()
            ->shouldReturn(['odd' => 3, 'even' => 1]);

        $this
            ->countBy(function ($k, $v) {
                return ($k + $v) % 2 == 0 ? 'even' : 'odd';
            })
            ->toArray()
            ->shouldReturn(['odd' => 3, 'even' => 1]);
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

    public function it_can_check_if_every_item_passes_predicament_test(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->every(function ($v) {
                return $v > 0;
            })
            ->shouldReturn(true);

        $this
            ->every(function ($v) {
                return $v > 1;
            })
            ->shouldReturn(false);

        $this
            ->every(function ($v, $k) {
                return $v > 0 && $k >= 0;
            })
            ->shouldReturn(true);

        $this
            ->every(function ($v, $k) {
                return $v > 0 && $k > 0;
            })
            ->shouldReturn(false);
    }

    public function it_can_check_if_some_items_pass_predicament_test(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->some(function ($v) {
                return $v < -1;
            })
            ->shouldReturn(false);

        $this
            ->some(function ($v, $k) {
                return $v > 0 && $k < -1;
            })
            ->shouldReturn(false);

        $this
            ->some(function ($v) {
                return $v < 2;
            })
            ->shouldReturn(true);

        $this
            ->some(function ($v, $k) {
                return $v > 0 && $k > 0;
            })
            ->shouldReturn(true);
    }

    public function it_can_check_if_it_contains_a_value(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->contains(3)
            ->shouldReturn(true);

        $this
            ->contains(true)
            ->shouldReturn(false);
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

    public function it_can_return_only_first_x_elements(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->take(2)
            ->toArray()
            ->shouldReturn([1, 3]);
    }

    public function it_can_skip_first_x_elements(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->drop(2)
            ->toArray()
            ->shouldReturn([2 => 3, 3 => 2]);
    }

    public function it_can_return_values(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2]);
        $this->values()->toArray()->shouldReturn([1, 2]);
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

    public function it_can_get_keys(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->keys()
            ->toArray()
            ->shouldReturn([0, 1, 2, 3]);
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

    public function it_can_drop_elements_from_end_of_the_collection(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->dropLast()
            ->toArray()
            ->shouldReturn([1, 3, 3]);

        $this
            ->dropLast(2)
            ->toArray()
            ->shouldReturn([1, 3]);
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

    public function it_can_append_item(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->append(1)
            ->values()
            ->toArray()
            ->shouldReturn([1, 3, 3, 2, 1]);
    }

    public function it_can_append_item_with_key(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->append(1, 'a')
            ->toArray()
            ->shouldReturn([1, 3, 3, 2, 'a' => 1]);
    }

    public function it_can_drop_items_while_predicament_is_true(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->dropWhile(function ($v) {
                return $v < 3;
            })
            ->toArray()
            ->shouldReturn([1 => 3, 2 => 3, 3 => 2]);

        $this
            ->dropWhile(function ($v, $k) {
                return $k < 2 && $v < 3;
            })
            ->toArray()
            ->shouldReturn([1 => 3, 2 => 3, 3 => 2]);
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

    public function it_can_replace_items_by_items_from_another_collection(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->replace([3 => 'a'])
            ->toArray()
            ->shouldReturn([1, 'a', 'a', 2]);
    }

    public function it_can_get_reduction_steps(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->reductions(
                function ($tmp, $i) {
                    return $tmp + $i;
                },
                0
            )
            ->toArray()
            ->shouldReturn([0, 1, 4, 7, 9]);
    }

    public function it_can_return_every_nth_item(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->takeNth(2)
            ->toArray()
            ->shouldReturn([1, 2 => 3]);
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

    public function it_can_get_nth_value(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->first(0)->shouldReturn(1);
        $this->values()->get(3)->shouldReturn(2);
    }

    public function it_can_create_infinite_collection_of_repeated_values(): void
    {
        $this->beConstructedThrough('repeat', [1]);
        $this->take(3)->toArray()->shouldReturn([1, 1, 1]);
    }

    public function it_can_create_finite_collection_of_repeated_values(): void
    {
        $this->beConstructedThrough('repeat', [1, 1]);
        $this->toArray()->shouldReturn([1]);
    }

    public function it_can_create_range_from_value_to_infinity(): void
    {
        $this->beConstructedThrough('range', [5]);
        $this->take(2)->toArray()->shouldReturn([5, 6]);
    }

    public function it_can_create_range_from_value_to_another_value(): void
    {
        $this->beConstructedThrough('range', [5, 6]);
        $this->take(4)->toArray()->shouldReturn([5, 6]);
    }

    public function it_can_check_if_it_is_not_empty(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this->isEmpty()->shouldReturn(false);
        $this->isNotEmpty()->shouldReturn(true);
    }

    public function it_can_check_if_it_is_empty(): void
    {
        $this->beConstructedWith([]);

        $this->isEmpty()->shouldReturn(true);
        $this->isNotEmpty()->shouldReturn(false);
    }

    public function it_can_check_frequency_of_distinct_items(): void
    {
        $this->beConstructedWith([1, 3, 3, 2,]);

        $this
            ->frequencies()
            ->toArray()
            ->shouldReturn([1 => 1, 3 => 2, 2 => 1]);
    }

    public function it_can_get_first_item(): void
    {
        $this->beConstructedWith([1, [2], 3]);
        $this->first()->shouldReturn(1);
        $this->drop(1)->first()->shouldReturn([2]);
        $this->drop(1)->first(true)->toArray()->shouldReturn([2]);
    }

    public function it_will_throw_when_trying_to_get_first_item_of_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(ItemNotFound::class)->during('first');
    }

    public function it_can_get_last_item(): void
    {
        $this->beConstructedWith([1, [2], 3]);
        $this->last()->shouldReturn(3);
        $this->take(2)->last()->shouldReturn([2]);
        $this->take(2)->last(true)->toArray()->shouldReturn([2]);
    }

    public function it_will_throw_when_trying_to_get_last_item_of_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->shouldThrow(ItemNotFound::class)->during('last');
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

    public function it_can_combine_the_collection(): void
    {
        $this->beConstructedWith(['a', 'b']);
        $this->combine([1, 2])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
        $this->combine([1])->toArray()->shouldReturn(['a' => 1]);
        $this->combine([1, 2, 3])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
    }

    public function it_can_get_second_item(): void
    {
        $this->beConstructedWith([1, 2]);
        $this->second()->shouldReturn(2);
    }

    public function it_throws_when_trying_to_get_non_existent_second_item(): void
    {
        $this->beConstructedWith([1]);
        $this->shouldThrow(ItemNotFound::class)->during('second');
    }

    public function it_can_drop_item_by_key(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2]);
        $this->except(['a', 'b'])->toArray()->shouldReturn([]);
    }

    public function it_can_get_the_difference_between_collections(): void
    {
        $this->beConstructedWith([1, 2, 3, 4]);
        $this->diff([1, 2])->toArray()->shouldReturn([2 => 3, 3 => 4]);
        $this->diff([1, 2], [3])->toArray()->shouldReturn([3 => 4]);
    }

    public function it_can_flip_the_collection(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2]);
        $this->flip()->toArray()->shouldReturn([1 => 'a', 2 => 'b']);
    }

    public function it_can_check_if_key_exits(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2]);
        $this->has('a')->shouldReturn(true);
        $this->has('x')->shouldReturn(false);
    }

    public function it_filters_by_keys(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->only(['a', 'b'])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
        $this->only(['a', 'b', 'x'])->toArray()->shouldReturn(['a' => 1, 'b' => 2]);
    }

    public function it_can_serialize_and_unserialize(): void
    {
        $original = Collection::from([1, 2, 3])->take(2);
        $this->beConstructedWith([1, 2, 3, 4]);
        $this->unserialize($original->serialize());
        $this->toArray()->shouldReturn([1, 2]);
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

    public function it_can_extract_data_from_nested_collections(): void
    {
        $input = [
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
        ];
        $this->beConstructedWith($input);

        $this->extract('')->toArray()->shouldReturn($input);
        $this->extract('a.b')->toArray()->shouldReturn([1, 2]);
        $this->extract('*.b')->toArray()->shouldReturn([1, 2, 3, 4, 5]);
        $this->extract('\*.b')->toArray()->shouldReturn([3]);
        $this->extract('\..b')->toArray()->shouldReturn([4]);
    }

    public function it_can_get_the_intersect_of_collections(): void
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->intersect([1, 2])->values()->toArray()->shouldReturn([1, 2]);
        $this->intersect([1], [3])->values()->toArray()->shouldReturn([1, 3]);
    }

    public function it_can_check_if_size_is_exactly_n(): void
    {
        $this->beConstructedWith([1, 2]);
        $this->sizeIs(2)->shouldReturn(true);
        $this->sizeIs(3)->shouldReturn(false);
        $this->sizeIs(0)->shouldReturn(false);
    }

    public function it_can_check_if_size_is_less_than_n(): void
    {
        $this->beConstructedWith([1, 2]);
        $this->sizeIsLessThan(0)->shouldReturn(false);
        $this->sizeIsLessThan(2)->shouldReturn(false);
        $this->sizeIsLessThan(3)->shouldReturn(true);
    }

    public function it_can_check_if_size_is_greater_than_n(): void
    {
        $this->beConstructedWith([1, 2]);
        $this->sizeIsGreaterThan(2)->shouldReturn(false);
        $this->sizeIsGreaterThan(1)->shouldReturn(true);
        $this->sizeIsGreaterThan(0)->shouldReturn(true);
    }

    public function it_can_check_if_size_is_between_n_and_m(): void
    {
        $this->beConstructedWith([1, 2]);
        $this->sizeIsBetween(1, 3)->shouldReturn(true);
        $this->sizeIsBetween(3, 4)->shouldReturn(false);
        $this->sizeIsBetween(0, 0)->shouldReturn(false);
        $this->sizeIsBetween(3, 1)->shouldReturn(true);
    }

    public function it_can_sum_the_collection(): void
    {
        $this->beConstructedWith([1, 2, 3, 4]);
        $this->sum()->shouldReturn(10);
        $this->append(1.5)->sum()->shouldReturn(11.5);
    }

    public function it_can_get_average_of_the_collection(): void
    {
        $this->beConstructedWith([1, 2, 2, 3]);
        $this->average()->shouldReturn(2);
        $this->append(3)->average()->shouldReturn(2.2);
    }

    public function it_will_return_zero_when_average_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->average()->shouldReturn(0);
    }

    public function it_can_get_maximal_value_in_the_colleciton(): void
    {
        $this->beConstructedWith([1, 2, 3, 2]);
        $this->max()->shouldReturn(3);
    }

    public function it_will_return_null_when_max_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->max()->shouldReturn(null);
    }

    public function it_can_get_min_value_in_the_colleciton(): void
    {
        $this->beConstructedWith([2, 1, 3, 2]);
        $this->min()->shouldReturn(1);
    }

    public function it_will_return_null_when_min_is_called_on_empty_collection(): void
    {
        $this->beConstructedWith([]);
        $this->min()->shouldReturn(null);
    }

    public function it_can_convert_the_collection_to_string(): void
    {
        $this->beConstructedWith([2, 'a', 3, null]);
        $this->toString()->shouldReturn('2a3');
    }

    public function it_can_replace_by_key(): void
    {
        $this->beConstructedWith(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->replaceByKeys(['b' => 3])->toArray()->shouldReturn(['a' => 1, 'b' => 3, 'c' => 3]);
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

    public function it_should_throw_an_invalid_argument_if_collection_items_are_not_collection(): void
    {
        $this->beConstructedWith([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $this->shouldThrow(InvalidArgument::class)->during('transpose');
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

    public function it_can_dump_the_collection(): void
    {
        $this->beConstructedWith(
            [
                [
                    [1, [2], 3],
                    ['a' => 'b'],
                    new ArrayIterator([1, 2, 3]),
                ],
                [1, 2, 3],
                new ArrayIterator(['a', 'b', 'c']),
                true,
                new \DusanKasan\Knapsack\Tests\Helpers\Car('sedan', 5),
                \DusanKasan\Knapsack\concat([1], [1]),
            ]
        );

        $this->dump()->shouldReturn(
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
        );

        $this->dump(2)->shouldReturn(
            [
                [
                    [1, [2], '>>>'],
                    ['a' => 'b'],
                    '>>>',
                ],
                [1, 2, '>>>'],
                '>>>',
            ]
        );

        $this->dump(null, 3)->shouldReturn(
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
        );

        $this->dump(2, 3)->shouldReturn(
            [
                [
                    [1, '^^^', '>>>'],
                    ['a' => 'b'],
                    '>>>',
                ],
                [1, 2, '>>>'],
                '>>>',
            ]
        );
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
}
