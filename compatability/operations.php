<?php

namespace DusanKasan\Knapsack;

use Nekman\Collection\Exceptions\ItemNotFound;
use Nekman\Collection\Exceptions\NoMoreItems;
use function Nekman\Collection\iterable_append;
use function Nekman\Collection\iterable_average;
use function Nekman\Collection\iterable_combine;
use function Nekman\Collection\iterable_concat;
use function Nekman\Collection\iterable_contains;
use function Nekman\Collection\iterable_count_by;
use function Nekman\Collection\iterable_cycle;
use function Nekman\Collection\iterable_dereference_key_value;
use function Nekman\Collection\iterable_diff;
use function Nekman\Collection\iterable_distinct;
use function Nekman\Collection\iterable_drop;
use function Nekman\Collection\iterable_drop_last;
use function Nekman\Collection\iterable_drop_while;
use function Nekman\Collection\iterable_each;
use function Nekman\Collection\iterable_every;
use function Nekman\Collection\iterable_except;
use function Nekman\Collection\iterable_extract;
use function Nekman\Collection\iterable_filter;
use function Nekman\Collection\iterable_find;
use function Nekman\Collection\iterable_first;
use function Nekman\Collection\iterable_flatten;
use function Nekman\Collection\iterable_flip;
use function Nekman\Collection\iterable_frequencies;
use function Nekman\Collection\iterable_get;
use function Nekman\Collection\iterable_get_or_default;
use function Nekman\Collection\iterable_group_by;
use function Nekman\Collection\iterable_group_by_key;
use function Nekman\Collection\iterable_has;
use function Nekman\Collection\iterable_index_by;
use function Nekman\Collection\iterable_interleave;
use function Nekman\Collection\iterable_interpose;
use function Nekman\Collection\iterable_intersect;
use function Nekman\Collection\iterable_is_empty;
use function Nekman\Collection\iterable_is_not_empty;
use function Nekman\Collection\iterable_iterate;
use function Nekman\Collection\iterable_keys;
use function Nekman\Collection\iterable_last;
use function Nekman\Collection\iterable_map;
use function Nekman\Collection\iterable_mapcat;
use function Nekman\Collection\iterable_max;
use function Nekman\Collection\iterable_min;
use function Nekman\Collection\iterable_only;
use function Nekman\Collection\iterable_partition;
use function Nekman\Collection\iterable_partition_by;
use function Nekman\Collection\iterable_prepend;
use function Nekman\Collection\iterable_range;
use function Nekman\Collection\iterable_realize;
use function Nekman\Collection\iterable_reduce;
use function Nekman\Collection\iterable_reduce_right;
use function Nekman\Collection\iterable_reductions;
use function Nekman\Collection\iterable_reject;
use function Nekman\Collection\iterable_repeat;
use function Nekman\Collection\iterable_replace;
use function Nekman\Collection\iterable_replace_by_keys;
use function Nekman\Collection\iterable_reverse;
use function Nekman\Collection\iterable_second;
use function Nekman\Collection\iterable_shuffle;
use function Nekman\Collection\iterable_size;
use function Nekman\Collection\iterable_size_is;
use function Nekman\Collection\iterable_size_is_between;
use function Nekman\Collection\iterable_size_is_greater_than;
use function Nekman\Collection\iterable_size_is_less_than;
use function Nekman\Collection\iterable_slice;
use function Nekman\Collection\iterable_some;
use function Nekman\Collection\iterable_sort;
use function Nekman\Collection\iterable_split_at;
use function Nekman\Collection\iterable_split_with;
use function Nekman\Collection\iterable_sum;
use function Nekman\Collection\iterable_take;
use function Nekman\Collection\iterable_take_nth;
use function Nekman\Collection\iterable_take_while;
use function Nekman\Collection\iterable_to_array;
use function Nekman\Collection\iterable_to_string;
use function Nekman\Collection\iterable_transpose;
use function Nekman\Collection\iterable_values;
use function Nekman\Collection\iterable_zip;
use function Nekman\Collection\print_dump;

/**
 * @deprecated
 * @see iterable_to_array()
 */
function toArray(iterable $collection): array
{
    return iterable_to_array($collection);
}

/**
 * @deprecated
 * @see iterable_distinct()
 */
function distinct(iterable $collection): Collection
{
    return new Collection(iterable_distinct($collection));
}

/**
 * @deprecated
 * @see iterable_size()
 */
function size(iterable $collection): int
{
    return iterable_size($collection);
}

/**
 * @deprecated
 * @see iterable_reverse()
 */
function reverse(iterable $collection): Collection
{
    return new Collection(iterable_reverse($collection));
}

/**
 * @deprecated
 * @see iterable_values()
 */
function values(iterable $collection): Collection
{
    return new Collection(iterable_values($collection));
}

/**
 * @deprecated
 * @see iterable_keys()
 */
function keys(iterable $collection): Collection
{
    return new Collection(iterable_keys($collection));
}

/**
 * @deprecated
 * @see iterable_cycle()
 */
function cycle(iterable $collection): Collection
{
    return new Collection(iterable_cycle($collection));
}

/**
 * @deprecated
 * @see iterable_shuffle()
 */
function shuffle(iterable $collection): Collection
{
    return new Collection(iterable_shuffle($collection));
}

/**
 * @deprecated
 * @see iterable_is_empty()
 */
function isEmpty(iterable $collection): bool
{
    return iterable_is_empty($collection);
}

/**
 * @deprecated
 * @see iterable_is_not_empty()
 */
function isNotEmpty(iterable $collection): bool
{
    return iterable_is_not_empty($collection);
}

/**
 * @deprecated
 * @see iterable_frequencies()
 */
function frequencies(iterable $collection): Collection
{
    return new Collection(iterable_frequencies($collection));
}

/**
 * @deprecated
 * @see iterable_first()
 */
function first(iterable $collection): mixed
{
    return iterable_first($collection);
}

/**
 * @deprecated
 * @see last()
 */
function last(iterable $collection): mixed
{
    return iterable_last($collection);
}

/**
 * @deprecated
 * @see iterable_map()
 */
function map(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_map($collection, $function));
}

/**
 * @deprecated
 * @see iterable_filter()
 */
function filter(iterable $collection, ?callable $function = null): Collection
{
    return new Collection(iterable_filter($collection, $function));
}

/**
 * @deprecated
 * @see iterable_concat()
 */
function concat(iterable ...$collections): Collection
{
    return new Collection(iterable_concat($collections));
}

/**
 * @deprecated
 * @see iterable_reduce()
 */
function reduce(iterable $collection, callable $function, mixed $startValue): mixed
{
    return iterable_reduce($collection, $function, $startValue);
}

/**
 * @deprecated
 * @see iterable_flatten()
 */
function flatten(iterable $collection, int $levelsToFlatten = -1): Collection
{
    return new Collection(iterable_flatten($collection, $levelsToFlatten));
}

/**
 * @deprecated
 * @see iterable_sort()
 */
function sort(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_sort($collection, $function));
}

/**
 * @deprecated
 * @see iterable_slice()
 */
function slice(iterable $collection, int $from, int $to = -1): Collection
{
    return new Collection(iterable_slice($collection, $from, $to));
}

/**
 * @deprecated
 * @see iterable_group_by()
 */
function groupBy(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_group_by($collection, $function));
}

/**
 * @deprecated
 * @see iterable_group_by_key()
 */
function groupByKey(iterable $collection, mixed $key): Collection
{
    return new Collection(iterable_group_by_key($collection, $key));
}

/**
 * @deprecated
 * @see iterable_each()
 */
function each(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_each($collection, $function));
}

/**
 * @deprecated
 * @see iterable_get()
 */
function get(iterable $collection, mixed $key): mixed
{
    return iterable_get($collection, $key);
}

/**
 * @deprecated
 * @see iterable_get_or_default()
 */
function getOrDefault(iterable $collection, mixed $key, mixed $default): mixed
{
    return iterable_get_or_default($collection, $key, $default);
}

/**
 * @deprecated
 * @see iterable_find()
 */
function find(iterable $collection, callable $function, mixed $default = null): mixed
{
    return iterable_find($collection, $function, $default);
}

/**
 * @deprecated
 * @see iterable_index_by()
 */
function indexBy(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_index_by($collection, $function));
}

/**
 * @deprecated
 * @see iterable_count_by()
 */
function countBy(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_count_by($collection, $function));
}

/**
 * @deprecated
 * @see iterable_every()
 */
function every(iterable $collection, callable $function): bool
{
    return iterable_every($collection, $function);
}

/**
 * @deprecated
 * @see iterable_some()
 */
function some(iterable $collection, callable $function): bool
{
    return iterable_some($collection, $function);
}

/**
 * @deprecated
 * @see iterable_contains()
 */
function contains(iterable $collection, mixed $needle): bool
{
    return iterable_contains($collection, $needle);
}

/**
 * @deprecated
 * @see iterable_reduce_right()
 */
function reduceRight(iterable $collection, callable $function, mixed $startValue): mixed
{
    return iterable_reduce_right($collection, $function, $startValue);
}

/**
 * @deprecated
 * @see iterable_take()
 */
function take(iterable $collection, int $numberOfItems): Collection
{
    return new Collection(iterable_take($collection, $numberOfItems));
}

/**
 * @deprecated
 * @see iterable_drop()
 */
function drop(iterable $collection, int $numberOfItems): Collection
{
    return new Collection(iterable_drop($collection, $numberOfItems));
}

/**
 * @deprecated
 * @see iterable_iterate()
 */
function iterate(mixed $value, callable $function): Collection
{
    return new Collection(iterable_iterate($value, $function));
}

/**
 * @deprecated
 * @see iterable_reject()
 */
function reject(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_reject($collection, $function));
}

/**
 * @deprecated
 * @see iterable_drop_last()
 */
function dropLast(iterable $collection, int $numberOfItems = 1): Collection
{
    return new Collection(iterable_drop_last($collection, $numberOfItems));
}

/**
 * @deprecated
 * @see iterable_interpose()
 */
function interpose(iterable $collection, mixed $separator): Collection
{
    return new Collection(iterable_interpose($collection, $separator));
}

/**
 * @deprecated
 * @see iterable_interleave()
 */
function interleave(iterable ...$collections): Collection
{
    return new Collection(iterable_interleave($collections));
}

/**
 * @deprecated
 * @see iterable_prepend()
 */
function prepend(iterable $collection, mixed $value, mixed $key = null): Collection
{
    return new Collection(iterable_prepend($collection, $value, $key));
}

/**
 * @deprecated
 * @see iterable_append()
 */
function append(iterable $collection, mixed $value, mixed $key = null): Collection
{
    return new Collection(iterable_append($collection, $value, $key));
}

/**
 * @deprecated
 * @see iterable_drop_while()
 */
function dropWhile(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_drop_while($collection, $function));
}

/**
 * @deprecated
 * @see iterable_take_while()
 */
function takeWhile(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_take_while($collection, $function));
}

/**
 * @deprecated
 * @see iterable_mapcat()
 */
function mapcat(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_mapcat($collection, $function));
}

/**
 * @deprecated
 * @see iterable_split_at()
 */
function splitAt(iterable $collection, $position)
{
    return new Collection(iterable_split_at($collection, $position));
}

/**
 * @deprecated
 * @see iterable_split_with()
 */
function splitWith(iterable $collection, callable $function)
{
    return new Collection(iterable_split_with($collection, $function));
}

/**
 * @deprecated
 * @see iterable_replace()
 */
function replace(iterable $collection, iterable $replacementMap): Collection
{
    return new Collection(iterable_replace($collection, $replacementMap));
}

/**
 * @deprecated
 * @see iterable_reductions()
 */
function reductions(iterable $collection, callable $function, mixed $startValue): Collection
{
    return new Collection(iterable_reductions($collection, $function, $startValue));
}

/**
 * @deprecated
 * @see iterable_take_nth()
 */
function takeNth(iterable $collection, int $step): Collection
{
    return new Collection(iterable_take_nth($collection, $step));
}

/**
 * @deprecated
 * @see iterable_partition()
 */
function partition(iterable $collection, int $numberOfItems, int $step = -1, iterable $padding = []): Collection
{
    return new Collection(iterable_partition($collection, $numberOfItems, $step, $padding));
}

/**
 * @deprecated
 * @see iterable_partition_by()
 */
function partitionBy(iterable $collection, callable $function): Collection
{
    return new Collection(iterable_partition_by($collection, $function));
}

/**
 * @deprecated
 * @see iterable_repeat()
 */
function repeat(mixed $value, int $times = -1): Collection
{
    return new Collection(iterable_repeat($value, $times));
}

/**
 * @deprecated
 * @see iterable_range()
 */
function range(int $start = 0, ?int $end = null, int $step = 1): Collection
{
    return new Collection(iterable_range($start, $end, $step));
}

/**
 * @deprecated
 * @see is_iterable()
 */
function isCollection(mixed $input): bool
{
    return is_iterable($input);
}

/**
 * @deprecated
 * @see \Nekman\Collection\duplicate()
 */
function duplicate(mixed $input): mixed
{
    return \Nekman\Collection\duplicate($input);
}

/**
 * @deprecated
 * @see iterable_dereference_key_value()
 */
function dereferenceKeyValue(iterable $collection): Collection
{
    return new Collection(iterable_dereference_key_value($collection));
}

/**
 * @deprecated
 * @see iterable_realize()
 */
function realize(iterable $collection): Collection
{
    return new Collection(iterable_realize($collection));
}

/**
 * @deprecated
 * @see iterable_second()
 */
function second(iterable $collection): mixed
{
    return iterable_second($collection);
}

/**
 * @deprecated
 * @see iterable_combine()
 */
function combine(iterable $keys, iterable $values): Collection
{
    return new Collection(iterable_combine($keys, $values));
}

/**
 * @deprecated
 * @see iterable_except()
 */
function except(iterable $collection, iterable $keys): Collection
{
    return new Collection(iterable_except($collection, $keys));
}

/**
 * @deprecated
 * @see iterable_only()
 */
function only(iterable $collection, iterable $keys): Collection
{
    return new Collection(iterable_only($collection, $keys));
}

/**
 * @deprecated
 * @see iterable_diff()
 */
function diff(iterable $collection, iterable ...$collections): Collection
{
    return new Collection(iterable_diff($collection, $collections));
}

/**
 * @deprecated
 * @see iterable_intersect()
 */
function intersect(iterable $collection, iterable ...$collections): Collection
{
    return new Collection(iterable_intersect($collection, $collections));
}

/**
 * @deprecated
 * @see iterable_flip()
 */
function flip(iterable $collection): Collection
{
    return new Collection(iterable_flip($collection));
}

/**
 * @deprecated
 * @see iterable_has()
 */
function has(iterable $collection, mixed $key): bool
{
    return iterable_has($collection, $key);
}

/**
 * @deprecated
 * @see iterable_zip()
 */
function zip(iterable ...$collections): Collection
{
    return new Collection(iterable_zip($collections));
}

/**
 * @deprecated
 * @see iterable_transpose()
 */
function transpose(iterable $collection): Collection
{
    return new Collection(iterable_transpose($collection));
}

/**
 * @deprecated
 * @see iterable_extract()
 */
function extract(iterable $collection, mixed $keyPath): Collection
{
    return new Collection(iterable_extract($collection, $keyPath));
}

/**
 * @deprecated
 * @see iterable_size_is()
 */
function sizeIs(iterable $collection, int $size): bool
{
    return iterable_size_is($collection, $size);
}

/**
 * @deprecated
 * @see iterable_size_is_less_than()
 */
function sizeIsLessThan(iterable $collection, int $size): bool
{
    return iterable_size_is_less_than($collection, $size);
}

/**
 * @deprecated
 * @see iterable_size_is_greater_than()
 */
function sizeIsGreaterThan(iterable $collection, int $size): bool
{
    return iterable_size_is_greater_than($collection, $size);
}

/**
 * @deprecated
 * @see iterable_size_is_between()
 */
function sizeIsBetween(iterable $collection, int $fromSize, int $toSize): bool
{
    return iterable_size_is_between($collection, $fromSize, $toSize);
}

/**
 * @deprecated
 * @see iterable_sum()
 */
function sum(iterable $collection): int|float
{
    return iterable_sum($collection);
}

/**
 * @deprecated
 * @see iterable_average()
 */
function average(iterable $collection): int|float
{
    return iterable_average($collection);
}

/**
 * @deprecated
 * @see iterable_max()
 */
function max(iterable $collection): mixed
{
    return iterable_max($collection);
}

/**
 * @deprecated
 * @see iterable_min()
 */
function min(iterable $collection): mixed
{
    return iterable_min($collection);
}

/**
 * @deprecated
 * @see iterable_to_string()
 */
function toString(iterable $collection): string
{
    return iterable_to_string($collection);
}


/**
 * @deprecated
 * @see iterable_replace_by_keys()
 */
function replaceByKeys(iterable $collection, iterable $replacementMap)
{
    return new Collection(iterable_replace_by_keys($collection, $replacementMap));
}

/**
 * @deprecated
 * @see \Nekman\Collection\dump()
 */
function dump(mixed $input, ?int $maxItemsPerCollection = null, ?int $maxDepth = null): mixed
{
    return \Nekman\Collection\dump($input, $maxItemsPerCollection, $maxDepth);
}

/**
 * @deprecated
 * @see print_dump()
 */
function printDump(mixed $input, ?int $maxItemsPerCollection = null, ?int $maxDepth = null): mixed
{
    return print_dump($input, $maxItemsPerCollection, $maxDepth);
}
