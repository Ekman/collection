<?php

namespace Nekman\Collection;

use ArrayIterator;
use Nekman\Collection\Contracts\CollectionInterface;
use Nekman\Collection\Exceptions\InvalidArgument;
use Traversable;

class Collection implements CollectionInterface
{
    private iterable $it;

    public function __construct(iterable|callable $it = [])
    {
        $it = match (true) {
            $it instanceof self => $it->it,
            is_callable($it) => $it(),
            default => $it,
        };

        if (!is_iterable($it)) {
            throw new InvalidArgument;
        }

        $this->it = is_array($it) ? new ArrayIterator($it) : $it;
    }

    public static function from(iterable|callable $it = []): self
    {
        return $it instanceof self ? $it : new self($it);
    }

    public static function iterate(mixed $value, callable $iterable): self
    {
        return new self(iterable_iterate($value, $iterable));
    }

    public static function range(int $start = 0, ?int $end = null, int $step = 1): self
    {
        return new self(iterable_range($start, $end, $step));
    }

    public static function repeat(mixed $startValue, int $nItems = -1): self
    {
        return new self(iterable_repeat($startValue, $nItems));
    }

    public function __clone(): void
    {
        $this->it = clone $this->it;
    }

    final public function __serialize(): array
    {
        return $this->toArray();
    }

    public function toArray(bool $onlyValues = false): array
    {
        return $this->it = iterable_to_array($this->it, $onlyValues);
    }

    final public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return iterable_to_string($this->it);
    }

    final public function __unserialize(array $data): void
    {
        $this->it = $data;
    }

    public function append(mixed $value, mixed $key = null): self
    {
        return new self(iterable_append($this->it, $value, $key));
    }

    public function average(): float|int
    {
        return iterable_average($this->it);
    }

    public function combine(iterable $keys): self
    {
        return new self(iterable_combine($this->it, $keys));
    }

    public function concat(iterable ...$its): self
    {
        return new self(iterable_concat($this->it, ...$its));
    }

    public function contains(mixed $needle): bool
    {
        return iterable_contains($this->it, $needle);
    }

    final public function count(): int
    {
        return $this->size();
    }

    public function size(): int
    {
        return iterable_size($this->it);
    }

    public function countBy(callable $countBy): CollectionInterface
    {
        return new self(iterable_count_by($this->it, $countBy));
    }

    public function cycle(): self
    {
        return new self(iterable_cycle($this->it));
    }

    public function dereferenceKeyValue(): self
    {
        return new self(iterable_dereference_key_value($this->it));
    }

    public function diff(iterable ...$its): self
    {
        return new self(iterable_diff($this->it, ...$its));
    }

    public function distinct(): self
    {
        return new self(iterable_distinct($this->it));
    }

    public function drop(int $nItems): self
    {
        return new self(iterable_drop($this->it, $nItems));
    }

    public function dropLast(int $nItems = 1): self
    {
        return new self(iterable_drop_last($this->it, $nItems));
    }

    public function dropWhile(callable $dropWhile): self
    {
        return new self(iterable_drop_while($this->it, $dropWhile));
    }

    public function dump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): array
    {
        return dump($this->it, $maxItemsPerCollection, $maxDepth);
    }

    public function each(callable $each): self
    {
        return new self(iterable_each($this->it, $each));
    }

    public function every(callable $every): bool
    {
        return iterable_every($this->it, $every);
    }

    public function except(iterable $keys): self
    {
        return new self(iterable_except($this->it, $keys));
    }

    public function extract(string $keyPath): self
    {
        return new self(iterable_extract($this->it, $keyPath));
    }

    public function filter(?callable $filter = null): self
    {
        return new self(iterable_filter($this->it, $filter));
    }

    public function find(callable $find, mixed $default = null, bool $convertToCollection = false): mixed
    {
        $result = iterable_find($this->it, $find, $default);
        return $convertToCollection && is_iterable($result) ? new self($result) : $result;
    }

    public function first(bool $convertToCollection = false): mixed
    {
        $first = iterable_first($this->it);
        return $convertToCollection && is_iterable($first) ? new self($first) : $first;
    }

    public function flatten(int $levelsToFlatten = -1): self
    {
        return new self(iterable_flatten($this->it, $levelsToFlatten));
    }

    public function flip(): self
    {
        return new self(iterable_flip($this->it));
    }

    public function frequencies(): self
    {
        return new self(iterable_frequencies($this->it));
    }

    public function get(mixed $key, bool $convertToCollection = false): mixed
    {
        $get = iterable_get($this->it, $key);
        return $convertToCollection && is_iterable($get) ? new self($get) : $get;
    }

    final public function getIterator(): Traversable
    {
        return $this->toTraversable();
    }

    public function toTraversable(): Traversable
    {
        return $this->it = new ArrayIterator(iterable_to_array($this->it));
    }

    public function getOrDefault(mixed $key, mixed $default = null, bool $convertToCollection = false): mixed
    {
        $getOrDefault = iterable_get_or_default($this->it, $key, $default);
        return $convertToCollection && is_iterable($getOrDefault) ? new self($getOrDefault) : $getOrDefault;
    }

    public function groupBy(callable $groupBy): self
    {
        $result = iterable_group_by($this->it, $groupBy);
        return new self(iterable_map($result, fn(iterable $it) => new self($it)));
    }

    public function groupByKey(mixed $key): self
    {
        $result = iterable_group_by_key($this->it, $key);
        return new self(iterable_map($result, fn(iterable $it) => new self($it)));
    }

    public function has(mixed $key): bool
    {
        return iterable_has($this->it, $key);
    }

    public function indexBy(callable $indexBy): self
    {
        return new self(iterable_index_by($this->it, $indexBy));
    }

    public function interleave(iterable ...$its): self
    {
        return new self(iterable_interleave($this->it, ...$its));
    }

    public function interpose(mixed $separator): self
    {
        return new self(iterable_interpose($this->it, $separator));
    }

    public function intersect(iterable ...$its): self
    {
        return new self(iterable_intersect($this->it, ...$its));
    }

    public function isEmpty(): bool
    {
        return iterable_is_empty($this->it);
    }

    public function isNotEmpty(): bool
    {
        return iterable_is_not_empty($this->it);
    }

    public function join(string $separator = ""): string
    {
        return iterable_join($this->it, $separator);
    }

    final public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function keys(): self
    {
        return new self(iterable_keys($this->it));
    }

    public function last(bool $convertToCollection = false): mixed
    {
        $last = iterable_last($this->it);
        return $convertToCollection && is_iterable($last) ? new self($last) : $last;
    }

    public function map(callable $map): self
    {
        return new self(iterable_map($this->it, $map));
    }

    public function mapcat(callable $mapcat): self
    {
        return new self(iterable_mapcat($this->it, $mapcat));
    }

    public function max(): mixed
    {
        return iterable_max($this->it);
    }

    public function min(): mixed
    {
        return iterable_min($this->it);
    }

    public function only(iterable $keys): self
    {
        return new self(iterable_only($this->it, $keys));
    }

    public function partition(int $nItems, int $step = 0, iterable $padding = []): self
    {
        $partition = iterable_partition($this->it, $nItems, $step, $padding);
        $partition = iterable_map($partition, fn($it) => is_iterable($it) ? new self($it) : $it);

        return new self($partition);
    }

    public function partitionBy(callable $partitionBy): self
    {
        return new self(iterable_partition_by($this->it, $partitionBy));
    }

    public function prepend(mixed $value, mixed $key = null): self
    {
        return new self(iterable_prepend($this->it, $value, $key));
    }

    public function printDump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): CollectionInterface
    {
        return new self(print_dump($this->it, $maxItemsPerCollection, $maxDepth));
    }

    public function realize(): self
    {
        return new self(iterable_realize($this->it));
    }

    public function reduce(callable $reduce, mixed $startValue, bool $convertToCollection = false): mixed
    {
        $reduce = iterable_reduce($this->it, $reduce, $startValue);
        return $convertToCollection && is_iterable($reduce) ? new self($reduce) : $reduce;
    }

    public function reduceRight(callable $reduceRight, mixed $startValue, bool $convertToCollection = false): mixed
    {
        $reduceRight = iterable_reduce_right($this->it, $reduceRight, $startValue);
        return $convertToCollection && is_iterable($reduceRight) ? new self($reduceRight) : $reduceRight;
    }

    public function reductions(callable $reductions, mixed $startValue): self
    {
        return new self(iterable_reductions($this->it, $reductions, $startValue));
    }

    public function referenceKeyValue(): self
    {
        return new self(iterable_reference_key_value($this->it));
    }

    public function reject(callable $reject): self
    {
        return new self(iterable_reject($this->it, $reject));
    }

    public function replace(iterable $replace): CollectionInterface
    {
        return new self(iterable_replace($this->it, $replace));
    }

    public function replaceByKeys(iterable $replace): self
    {
        return new self(iterable_replace_by_keys($this->it, $replace));
    }

    public function reverse(): self
    {
        return new self(iterable_reverse($this->it));
    }

    public function second(bool $convertToCollection = false): mixed
    {
        $second = iterable_second($this->it);
        return $convertToCollection && is_iterable($second) ? new self($second) : $second;
    }

    public function shuffle(): self
    {
        return new self(iterable_shuffle($this->it));
    }

    public function sizeIs(int $size): bool
    {
        return iterable_size_is($this->it, $size);
    }

    public function sizeIsBetween(int $fromSize, int $toSize): bool
    {
        return iterable_size_is_between($this->it, $fromSize, $toSize);
    }

    public function sizeIsGreaterThan(int $size): bool
    {
        return iterable_size_is_greater_than($this->it, $size);
    }

    public function sizeIsLessThan(int $size): bool
    {
        return iterable_size_is_less_than($this->it, $size);
    }

    public function slice(int $from, int $to = -1): self
    {
        return new self(iterable_slice($this->it, $from, $to));
    }

    public function some(callable $some): bool
    {
        return iterable_some($this->it, $some);
    }

    public function sort(callable $sort): self
    {
        return new self(iterable_sort($this->it, $sort));
    }

    public function splitAt(int $position): self
    {
        return new self(iterable_split_at($this->it, $position));
    }

    public function splitWith(callable $splitWith): self
    {
        return new self(iterable_split_with($this->it, $splitWith));
    }

    public function sum(): float|int
    {
        return iterable_sum($this->it);
    }

    public function take(int $nItems): self
    {
        return new self(iterable_take($this->it, $nItems));
    }

    public function takeNth(int $step): self
    {
        return new self(iterable_take_nth($this->it, $step));
    }

    public function takeWhile(callable $takeWhile): self
    {
        return new self(iterable_take_while($this->it, $takeWhile));
    }

    public function toArrayRecursive(bool $onlyValues = false): array
    {
        return $this->it = iterable_to_array_recursive($this->it, $onlyValues);
    }

    public function transform(callable $transform): self
    {
        $newTransform = fn(iterable $item) => $transform($item instanceof CollectionInterface ? $item : new Collection($item));

        return new self(iterable_transform($this->it, $newTransform));
    }

    public function transpose(iterable ...$its): self
    {
        $result = iterable_transpose($this->it, ...$its);
        return new self(iterable_map($result, fn(iterable $it) => new self($it)));
    }

    public function values(): self
    {
        return new self(iterable_values($this->it));
    }

    public function zip(iterable ...$its): self
    {
        return new self(iterable_zip($this->it, ...$its));
    }
}
