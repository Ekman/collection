<?php

namespace DusanKasan\Knapsack;

use Nekman\Collection\Exceptions\InvalidReturnValue;
use Nekman\Collection\Exceptions\ItemNotFound;
use function Nekman\Collection\dump;
use function Nekman\Collection\iterable_append;
use function Nekman\Collection\iterable_average;
use function Nekman\Collection\iterable_combine;
use function Nekman\Collection\iterable_concat;
use function Nekman\Collection\iterable_contains;
use function Nekman\Collection\iterable_count_by;
use function Nekman\Collection\iterable_cycle;
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
use function Nekman\Collection\iterable_realize;
use function Nekman\Collection\iterable_reduce;
use function Nekman\Collection\iterable_reduce_right;
use function Nekman\Collection\iterable_reductions;
use function Nekman\Collection\iterable_reject;
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
use function Nekman\Collection\iterable_transform;
use function Nekman\Collection\iterable_transpose;
use function Nekman\Collection\iterable_values;
use function Nekman\Collection\iterable_zip;
use function Nekman\Collection\print_dump;

/**
 * @deprecated Trait will be removed in next major version. No replacements.
 */
trait CollectionTrait
{
    public function append(mixed $value, mixed $key = null): Collection
    {
        return new Collection(iterable_append($this->getItems(), $value, $key));
    }

    protected function getItems(): iterable
    {
        return $this;
    }

    public function average(): int|float
    {
        return iterable_average($this->getItems());
    }

    public function combine(iterable $collection): Collection
    {
        try {
            return new Collection(iterable_combine($this->getItems(), $collection));
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function concat(iterable ...$collections): Collection
    {
        return new Collection(iterable_concat($this->getItems(), ...$collections));
    }

    public function contains(mixed $value): bool
    {
        return iterable_contains($this->getItems(), $value);
    }

    public function countBy(callable $function): Collection
    {
        return new Collection(iterable_count_by($this->getItems(), $function));
    }

    public function cycle(): Collection
    {
        return new Collection(iterable_cycle($this->getItems()));
    }

    public function diff(iterable ...$collections): Collection
    {
        return new Collection(iterable_diff($this->getItems(), ...$collections));
    }

    public function distinct(): Collection
    {
        return new Collection(iterable_distinct($this->getItems()));
    }

    public function drop(int $numberOfItems): Collection
    {
        return new Collection(iterable_drop($this->getItems(), $numberOfItems));
    }

    public function dropLast(int $numberOfItems = 1): Collection
    {
        return new Collection(iterable_drop_last($this->getItems(), $numberOfItems));
    }

    public function dropWhile(callable $function): Collection
    {
        return new Collection(iterable_drop_while($this->getItems(), $function));
    }

    public function dump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): array
    {
        return dump($this->getItems(), $maxItemsPerCollection, $maxDepth);
    }

    public function each(callable $function): Collection
    {
        return new Collection(iterable_each($this->getItems(), $function));
    }

    public function every(callable $function): bool
    {
        return iterable_every($this->getItems(), $function);
    }

    public function except(iterable $keys): Collection
    {
        return new Collection(iterable_except($this->getItems(), $keys));
    }

    public function extract(mixed $keyPath): Collection
    {
        return new Collection(iterable_extract($this->getItems(), $keyPath));
    }

    public function filter(?callable $function = null): Collection
    {
        return new Collection(iterable_filter($this->getItems(), $function));
    }

    public function find(callable $function, mixed $default = null, bool $convertToCollection = false): mixed
    {
        $result = iterable_find($this->getItems(), $function, $default);

        return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
    }

    public function first(bool $convertToCollection = false): Collection
    {
        try {
            $result = iterable_first($this->getItems());

            return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function flatten(int $depth = -1): Collection
    {
        return new Collection(iterable_flatten($this->getItems(), $depth));
    }

    public function flip(): Collection
    {
        return new Collection(iterable_flip($this->getItems()));
    }

    public function frequencies(): Collection
    {
        return new Collection(iterable_frequencies($this->getItems()));
    }

    public function get(mixed $key, bool $convertToCollection = false): mixed
    {
        try {
            $result = iterable_get($this->getItems(), $key);

            return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function getOrDefault(mixed $key, mixed $default = null, bool $convertToCollection = false): Collection
    {
        try {
            $result = iterable_get_or_default($this->getItems(), $key, $default);

            return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function groupBy(callable $function): Collection
    {
        return new Collection(iterable_group_by($this->getItems(), $function));
    }

    public function groupByKey(mixed $key): Collection
    {
        return new Collection(iterable_group_by_key($this->getItems(), $key));
    }

    public function has(mixed $key): bool
    {
        return iterable_has($this->getItems(), $key);
    }

    public function indexBy(callable $function): Collection
    {
        return new Collection(iterable_index_by($this->getItems(), $function));
    }

    public function interleave(iterable ...$collections): Collection
    {
        return new Collection(iterable_interleave($this->getItems(), ...$collections));
    }

    public function interpose(mixed $separator): Collection
    {
        return new Collection(iterable_interpose($this->getItems(), $separator));
    }

    public function intersect(iterable ...$collections): Collection
    {
        return new Collection(iterable_intersect($this->getItems(), ...$collections));
    }

    public function isEmpty(): bool
    {
        return iterable_is_empty($this->getItems());
    }

    public function isNotEmpty(): bool
    {
        return iterable_is_not_empty($this->getItems());
    }

    public function keys(): Collection
    {
        return new Collection(iterable_keys($this->getItems()));
    }

    public function last(bool $convertToCollection = false): mixed
    {
        try {
            $result = iterable_last($this->getItems());

            return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function map(callable $function): Collection
    {
        return new Collection(iterable_map($this->getItems(), $function));
    }

    public function mapcat(callable $function): Collection
    {
        return new Collection(iterable_mapcat($this->getItems(), $function));
    }

    public function max(): mixed
    {
        return iterable_max($this->getItems());
    }

    public function min(): mixed
    {
        return iterable_min($this->getItems());
    }

    public function only(iterable $keys): Collection
    {
        return new Collection(iterable_only($this->getItems(), $keys));
    }

    public function partition(int $numberOfItems, int $step = 0, iterable $padding = []): Collection
    {
        return new Collection(iterable_partition($this->getItems(), $numberOfItems, $step, $padding));
    }

    public function partitionBy(callable $function): Collection
    {
        return new Collection(iterable_partition_by($this->getItems(), $function));
    }

    public function prepend(mixed $value, mixed $key = null): Collection
    {
        return new Collection(iterable_prepend($this->getItems(), $value, $key));
    }

    public function printDump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): Collection
    {
        return new Collection(print_dump($this->getItems(), $maxItemsPerCollection, $maxDepth));
    }

    public function realize(): Collection
    {
        return new Collection(iterable_realize($this->getItems()));
    }

    public function reduce(callable $function, mixed $startValue, bool $convertToCollection = false): mixed
    {
        $result = iterable_reduce($this->getItems(), $function, $startValue);

        return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
    }

    public function reduceRight(callable $function, mixed $startValue, bool $convertToCollection = false): Collection
    {
        $result = iterable_reduce_right($this->getItems(), $function, $startValue);

        return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
    }

    public function reductions(callable $function, mixed $startValue): Collection
    {
        return new Collection(iterable_reductions($this->getItems(), $function, $startValue));
    }

    public function reject(callable $function): Collection
    {
        return new Collection(iterable_reject($this->getItems(), $function));
    }

    public function replace(iterable $replacementMap): Collection
    {
        return new Collection(iterable_replace($this->getItems(), $replacementMap));
    }

    public function replaceByKeys(iterable $replacementMap): Collection
    {
        return new Collection(iterable_replace_by_keys($this->getItems(), $replacementMap));
    }

    public function reverse(): Collection
    {
        return new Collection(iterable_reverse($this->getItems()));
    }

    public function second(mixed $convertToCollection = false): Collection
    {
        try {
            $result = iterable_second($this->getItems());

            return ($convertToCollection && is_iterable($result)) ? new Collection($result) : $result;
        } catch (ItemNotFound) {
            throw new Exceptions\ItemNotFound;
        }
    }

    public function shuffle(): Collection
    {
        return new Collection(iterable_shuffle($this->getItems()));
    }

    public function size(): int
    {
        return iterable_size($this->getItems());
    }

    public function sizeIs(int $size): bool
    {
        return iterable_size_is($this->getItems(), $size);
    }

    public function sizeIsBetween(int $fromSize, int $toSize): bool
    {
        return iterable_size_is_between($this->getItems(), $fromSize, $toSize);
    }

    public function sizeIsGreaterThan(int $size): bool
    {
        return iterable_size_is_greater_than($this->getItems(), $size);
    }

    public function sizeIsLessThan(int $size): bool
    {
        return iterable_size_is_less_than($this->getItems(), $size);
    }

    public function slice(int $from, int $to = -1): Collection
    {
        return new Collection(iterable_slice($this->getItems(), $from, $to));
    }

    public function some(callable $function): bool
    {
        return iterable_some($this->getItems(), $function);
    }

    public function sort(callable $function): Collection
    {
        return new Collection(iterable_sort($this->getItems(), $function));
    }

    public function splitAt(int $position): Collection
    {
        return new Collection(iterable_split_at($this->getItems(), $position));
    }

    public function splitWith(callable $function): Collection
    {
        return new Collection(iterable_split_with($this->getItems(), $function));
    }

    public function sum(): int|float
    {
        return iterable_sum($this->getItems());
    }

    public function take(int $numberOfItems): Collection
    {
        return new Collection(iterable_take($this->getItems(), $numberOfItems));
    }

    public function takeNth(int $step): Collection
    {
        return new Collection(iterable_take_nth($this->getItems(), $step));
    }

    public function takeWhile(callable $function): Collection
    {
        return new Collection(iterable_take_while($this->getItems(), $function));
    }

    public function toArray(): array
    {
        return iterable_to_array($this->getItems());
    }

    public function toString(): string
    {
        return iterable_to_string($this->getItems());
    }

    public function transform(callable $transformer): Collection
    {
        try {
            $items = new Collection($this->getItems());
            return new Collection(iterable_transform($items, $transformer));
        } catch (InvalidReturnValue) {
            throw new Exceptions\InvalidReturnValue;
        }
    }

    public function transpose(): Collection
    {
        return new Collection(iterable_transpose($this->getItems()));
    }

    public function values(): Collection
    {
        return new Collection(iterable_values($this->getItems()));
    }

    public function zip(...$collections): Collection
    {
        return new Collection(iterable_zip($this->getItems(), ...$collections));
    }
}
