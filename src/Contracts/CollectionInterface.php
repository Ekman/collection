<?php

namespace Nekman\Collection\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;
use Traversable;

interface CollectionInterface extends IteratorAggregate, Countable, JsonSerializable, Stringable
{
    public function append(mixed $value, mixed $key = null): self;

    public function average(): float|int;

    public function combine(iterable $keys): self;

    public function concat(iterable ...$its): self;

    public function contains(mixed $needle): bool;

    public function map(callable $map): self;

    public function reduce(callable $reduce, mixed $initial): mixed;

    public function size(): int;

    public function sizeIsBetween(int $fromSize, int $toSize): bool;

    public function sizeIs(int $size): bool;

    public function sizeIsGreaterThan(int $size): bool;

    public function sizeIsLessThan(int $size): bool;

    public function sum(): float|int;

    public function toArray(bool $onlyValues = false): array;

    public function realize(): self;

    public function keys(): self;

    public function indexBy(callable $indexBy): self;

    public function flatten(int $levelsToFlatten = -1): self;

    public function mapcat(callable $mapcat): self;

    public function values(): self;

    public function sort(callable $sort): self;

    public function min(): mixed;

    public function max(): mixed;

    public function partition(int $nItems): self;

    public function toString(): string;

    public function cycle(): self;

    public function diff(iterable ...$its): self;

    public function distinct(): self;

    public function drop(int $nItems): self;

    public function dropLast(int $nItems = 1): self;

    public function dropWhile(callable $dropWhile): self;

    public function each(callable $each): self;

    public function every(callable $every): bool;

    public function extract(string $keyPath): self;

    public function filter(?callable $filter = null): self;

    public function find(mixed $needle, mixed $default = null): mixed;

    public function first(bool $convertToIterable = false): mixed;

    public function except(iterable $keys): self;

    public function reject(callable $reject): self;

    public function slice(int $from, int $to = -1): self;

    public function toTraversable(): Traversable;

    public function only(iterable $keys): self;

    public function flip(): self;

    public function get(mixed $key, bool $convertToIterable = false): mixed;

    public function getOrDefault(mixed $key, mixed $default = null, bool $convertToIterable = false): mixed;

    public function groupBy(callable $groupBy): self;

    public function groupByKey(mixed $key): self;

    public function has(mixed $key): bool;

    public function frequencies(): self;

    public function interleave(iterable ...$its): self;

    public function take(int $nItems): self;

    public function interpose(mixed $separator): self;

    public function intersect(iterable ...$its): self;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    public function last(bool $convertToIterable = false): mixed;

    public function partitionBy(callable $partitionBy): self;

    public function prepend(mixed $value, mixed $key = null): self;

    public function reduceRight(callable $reduceRight, mixed $initial): mixed;

    public function reverse(): self;

    public function reductions(callable $reductions, mixed $initial): self;

    public function join(string $separator = ""): string;

    public function dereferenceKeyValue(): self;

    public function referenceKeyValue(): self;

    public function zip(iterable ...$its): self;

    public function some(callable $some): bool;

    public function transpose(iterable ...$its): self;

    public function transform(callable $transformer): self;

    public function takeWhile(callable $takeWhile): self;

    public function takeNth(int $step): self;

    public function splitAt(int $position): self;

    public function splitWith(callable $splitWith): self;

    public function second(): mixed;

    public function shuffle(): self;
}
