<?php

namespace Nekman\Collection\Contracts;

use Countable;
use IteratorAggregate;
use JsonSerializable;
use Nekman\Collection\Exceptions\InvalidReturnValue;
use Nekman\Collection\Exceptions\ItemNotFound;
use Stringable;
use Traversable;

interface CollectionInterface extends IteratorAggregate, Countable, JsonSerializable, Stringable
{
    /**
     * Returns a lazy collection of items of this collection with $value added as last element. If $key is not provided
     * it will be next integer index.
     *
     * @param mixed $value
     * @param mixed $key
     * @return self
     */
    public function append(mixed $value, mixed $key = null): self;

    /**
     * Returns average of values from this collection.
     *
     * @return int|float
     */
    public function average(): float|int;

    /**
     * Combines the values of this collection as keys, with values of $collection as values.  The resulting collection
     * has length equal to the size of smaller collection.
     *
     * @param iterable $keys
     * @return self
     * @throws ItemNotFound
     */
    public function combine(iterable $keys): self;

    /**
     * Returns a lazy collection with items from all $collections passed as argument appended together
     *
     * @param iterable ...$its
     * @return self
     */
    public function concat(iterable ...$its): self;

    /**
     * Returns true if $value is present in the collection.
     *
     * @param mixed $needle
     * @return bool
     */
    public function contains(mixed $needle): bool;

    /**
     * Returns a non-lazy collection of items whose keys are the return values of $function and values are the number of
     * items in this collection for which the $function returned this value.
     *
     * @param callable $countBy
     * @return self
     */
    public function countBy(callable $countBy): self;

    /**
     * Returns an infinite lazy collection of items in this collection repeated infinitely.
     *
     * @return self
     */
    public function cycle(): self;

    /**
     * Converts items in collection from [$key, $value] to [$key => $value]
     *
     * @return $this
     */
    public function dereferenceKeyValue(): self;

    /**
     * Returns a lazy collection of items that are in $this but are not in any of the other arguments, indexed by the
     * keys from the first collection. Note that the ...$collections are iterated non-lazily.
     *
     * @param iterable ...$its
     * @return self
     */
    public function diff(iterable ...$its): self;

    /**
     * Returns a lazy collection of distinct items. The comparison is the same as in in_array.
     *
     * @return self
     */
    public function distinct(): self;

    /**
     * A form of slice that returns all but first $numberOfItems items.
     *
     * @param int $nItems
     * @return self
     */
    public function drop(int $nItems): self;

    /**
     * Returns a lazy collection with last $numberOfItems items skipped. These are still iterated over, just skipped.
     *
     * @param int $nItems
     * @return self
     */
    public function dropLast(int $nItems = 1): self;

    /**
     * Returns a lazy collection by removing items from this collection until first item for which $function returns
     * false.
     *
     * @param callable $dropWhile
     * @return self
     */
    public function dropWhile(callable $dropWhile): self;

    /**
     * Dumps this collection into array (recursively).
     *
     * - scalars are returned as they are,
     * - array of class name => properties (name => value and only properties accessible for this class)
     *   is returned for objects,
     * - arrays or Traversables are returned as arrays,
     * - for anything else result of calling gettype($input) is returned
     *
     * If specified, $maxItemsPerCollection will only leave specified number of items in collection,
     * appending a new element at end '>>>' if original collection was longer.
     *
     * If specified, $maxDepth will only leave specified n levels of nesting, replacing elements
     * with '^^^' once the maximum nesting level was reached.
     *
     * If a collection with duplicate keys is encountered, the duplicate keys (except the first one)
     * will be change into a format originalKey//duplicateCounter where duplicateCounter starts from
     * 1 at the first duplicate. So [0 => 1, 0 => 2] will become [0 => 1, '0//1' => 2]
     *
     * @param int|null $maxItemsPerCollection
     * @param int|null $maxDepth
     * @return array
     * @deprecated Will be removed next major version
     */
    public function dump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): array;

    /**
     * Returns a lazy collection in which $function is executed for each item.
     *
     * @param callable $each ($value, $key)
     * @return self
     */
    public function each(callable $each): self;

    /**
     * Returns true if $function returns true for every item in this collection, false otherwise.
     *
     * @param callable $every
     * @return bool
     */
    public function every(callable $every): bool;

    /**
     * Returns a lazy collection without the items associated to any of the keys from $keys.
     *
     * @param iterable $keys
     * @return self
     */
    public function except(iterable $keys): self;

    /**
     * Extracts data from collection items by dot separated key path. Supports the * wildcard.  If a key contains \ or
     * it must be escaped using \ character.
     *
     * @param mixed $keyPath
     * @return self
     */
    public function extract(string $keyPath): self;

    /**
     * Returns a lazy collection of items for which $function returned true.
     *
     * @param callable|null $filter ($value, $key)
     * @return self
     */
    public function filter(?callable $filter = null): self;

    /**
     * Returns first value matched by $function. If no value matches, return $default. If $convertToCollection is true
     * and the return value is a collection (array|Traversable) an instance of Collection will be returned.
     *
     * @param callable $find
     * @param mixed $default
     * @param bool $convertToCollection
     * @return mixed
     */
    public function find(callable $find, mixed $default = null, bool $convertToCollection = false): mixed;

    /**
     * Returns first item of this collection. If the collection is empty, throws ItemNotFound. If $convertToCollection
     * is true and the return value is a collection (array|Traversable) an instance of Collection is returned.
     *
     * @param bool $convertToCollection
     * @return mixed
     * @throws ItemNotFound
     */
    public function first(bool $convertToCollection = false): mixed;

    /**
     * Returns a lazy collection with one or multiple levels of nesting flattened. Removes all nesting when no value
     * is passed.
     *
     * @param int $levelsToFlatten How many levels should be flattened, default (-1) is infinite.
     * @return self
     */
    public function flatten(int $levelsToFlatten = -1): self;

    /**
     * Returns a lazy collection where keys and values are flipped.
     *
     * @return self
     */
    public function flip(): self;

    /**
     * Returns a collection where keys are distinct items from this collection and their values are number of
     * occurrences of each value.
     *
     * @return self
     */
    public function frequencies(): self;

    /**
     * Returns value at the key $key. If multiple values have this key, return first. If no value has this key, throw
     * ItemNotFound. If $convertToCollection is true and the return value is a collection (array|Traversable) an
     * instance of Collection will be returned.
     *
     * @param mixed $key
     * @param bool $convertToCollection
     * @return mixed
     * @throws ItemNotFound
     */
    public function get(mixed $key, bool $convertToCollection = false): mixed;

    /**
     * Returns item at the key $key. If multiple items have this key, return first. If no item has this key, return
     * $ifNotFound. If no value has this key, throw ItemNotFound. If $convertToCollection is true and the return value
     * is a collection (array|Traversable) an instance of Collection will be returned.
     *
     * @param mixed $key
     * @param mixed $default
     * @param bool $convertToCollection
     * @return mixed
     * @throws ItemNotFound
     */
    public function getOrDefault(mixed $key, mixed $default = null, bool $convertToCollection = false): mixed;

    /**
     * Returns collection which items are separated into groups indexed by the return value of $function.
     *
     * @param callable $groupBy ($value, $key)
     * @return self
     */
    public function groupBy(callable $groupBy): self;

    /**
     * Returns collection where items are separated into groups indexed by the value at given key.
     *
     * @param mixed $key
     * @return self
     */
    public function groupByKey(mixed $key): self;

    /**
     * Checks for the existence of an item with$key in this collection.
     *
     * @param mixed $key
     * @return bool
     */
    public function has(mixed $key): bool;

    /**
     * Returns a lazy collection by changing keys of this collection for each item to the result of $function for
     * that item.
     *
     * @param callable $indexBy
     * @return self
     */
    public function indexBy(callable $indexBy): self;

    /**
     * Returns a lazy collection of first item from first collection, first item from second, second from first and
     * so on. Accepts any number of collections.
     *
     * @param iterable ...$its
     * @return self
     */
    public function interleave(iterable ...$its): self;

    /**
     * Returns a lazy collection of items of this collection separated by $separator
     *
     * @param mixed $separator
     * @return self
     */
    public function interpose(mixed $separator): self;

    /**
     * Returns a lazy collection of items that are in $this and all the other arguments, indexed by the keys from
     * the first collection. Note that the ...$collections are iterated non-lazily.
     *
     * @param iterable ...$its
     * @return self
     */
    public function intersect(iterable ...$its): self;

    /**
     * Returns true if this collection is empty. False otherwise.
     *
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Opposite of isEmpty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool;

    /**
     * Convert the collection to a string, separating each element with a $separator
     *
     * @param string $separator
     * @return string
     */
    public function join(string $separator = ""): string;

    /**
     * Returns a lazy collection of the keys of this collection.
     *
     * @return self
     */
    public function keys(): self;

    /**
     * Returns last item of this collection. If the collection is empty, throws ItemNotFound. If $convertToCollection
     * is true and the return value is a collection (array|Traversable) it is converted to Collection.
     *
     * @param bool $convertToCollection
     * @return mixed
     * @throws ItemNotFound
     */
    public function last(bool $convertToCollection = false): mixed;

    /**
     * Returns collection where each item is changed to the output of executing $function on each key/item.
     *
     * @param callable $map
     * @return self
     */
    public function map(callable $map): self;

    /**
     * Returns a lazy collection which is a result of calling map($function) and then flatten(1)
     *
     * @param callable $mapcat
     * @return self
     */
    public function mapcat(callable $mapcat): self;

    /**
     * Returns maximal value from this collection.
     *
     * @return mixed
     */
    public function max(): mixed;

    /**
     * Returns minimal value from this collection.
     *
     * @return mixed
     */
    public function min(): mixed;

    /**
     * Returns a lazy collection of items associated to any of the keys from $keys.
     *
     * @param iterable $keys
     * @return self
     */
    public function only(iterable $keys): self;

    /**
     * Returns a lazy collection of collections of $numberOfItems items each, at $step step
     * apart. If $step is not supplied, defaults to $numberOfItems, i.e. the partitions
     * do not overlap. If a $padding collection is supplied, use its elements as
     * necessary to complete last partition up to $numberOfItems items. In case there are
     * not enough padding elements, return a partition with less than $numberOfItems items.
     *
     * @param int $nItems
     * @param int $step
     * @param iterable $padding
     * @return self
     */
    public function partition(int $nItems, int $step = 0, iterable $padding = []): self;

    /**
     * Creates a lazy collection of collections created by partitioning this collection every time $function will
     * return different result.
     *
     * @param callable $partitionBy
     * @return self
     */
    public function partitionBy(callable $partitionBy): self;

    /**
     * Returns a lazy collection of items of this collection with $value added as first element. If $key is not provided
     * it will be next integer index.
     *
     * @param mixed $value
     * @param mixed $key
     * @return self
     */
    public function prepend(mixed $value, mixed $key = null): self;

    /**
     * Calls dump on this collection and then prints it using the var_export.
     *
     * @param int|null $maxItemsPerCollection
     * @param int|null $maxDepth
     * @return self
     * @deprecated Will be removed next major version
     */
    public function printDump(?int $maxItemsPerCollection = null, ?int $maxDepth = null): self;

    /**
     * Realizes collection - turns lazy collection into non-lazy one by iterating over it and storing the key/values.
     *
     * @return self
     */
    public function realize(): self;

    /**
     * Reduces the collection to single value by iterating over the collection and calling $function while
     * passing $startValue and current key/item as parameters. The output of $function is used as $startValue in
     * next iteration. The output of $function on last element is the return value of this function. If
     * $convertToCollection is true and the return value is a collection (array|Traversable) an instance of Collection
     * is returned.
     *
     * @param callable $reduce ($tmpValue, $value, $key)
     * @param mixed $startValue
     * @param bool $convertToCollection
     * @return mixed|self
     */
    public function reduce(callable $reduce, mixed $startValue, bool $convertToCollection = false): mixed;

    /**
     * Reduce the collection to single value. Walks from right to left. If $convertToCollection is true and the return
     * value is a collection (array|Traversable) an instance of Collection is returned.
     *
     * @param callable $reduceRight Must take 2 arguments, intermediate value and item from the iterator.
     * @param mixed $startValue
     * @param bool $convertToCollection
     * @return self
     */
    public function reduceRight(callable $reduceRight, mixed $startValue, bool $convertToCollection = false): mixed;

    /**
     * Returns a lazy collection of reduction steps.
     *
     * @param callable $reductions
     * @param mixed $startValue
     * @return self
     */
    public function reductions(callable $reductions, mixed $startValue): self;

    /**
     * Converts items in collection to [$key, $value]
     *
     * @return self
     */
    public function referenceKeyValue(): self;

    /**
     * Returns a lazy collection without elements matched by $function.
     *
     * @param callable $reject
     * @return self
     */
    public function reject(callable $reject): self;

    /**
     * Returns a lazy collection with items from this collection but values that are found in keys of $replacementMap
     * are replaced by their values.
     *
     * @param iterable $replace
     * @return self
     */
    public function replace(iterable $replace): self;

    /**
     * Returns a lazy collection with items from $collection, but items with keys  that are found in keys of
     * $replacementMap are replaced by their values.
     *
     * @param iterable $replace
     * @return self
     */
    public function replaceByKeys(iterable $replace): self;

    /**
     * Returns collection of items in this collection in reverse order.
     *
     * @return self
     */
    public function reverse(): self;

    /**
     * Returns the second item in this collection or throws ItemNotFound if the collection is empty or has 1 item. If
     * $convertToCollection is true and the return value is a collection (array|Traversable) it is converted to
     * Collection.
     *
     * @param bool $convertToCollection
     * @return mixed
     * @throws ItemNotFound
     */
    public function second(bool $convertToCollection = false): mixed;

    /**
     * Returns a non-collection of shuffled items from this collection
     *
     * @return self
     */
    public function shuffle(): self;

    /**
     * Returns the number of items in this collection.
     *
     * @return int
     */
    public function size(): int;

    /**
     * Checks whether this collection has exactly $size items.
     *
     * @param int $size
     * @return bool
     */
    public function sizeIs(int $size): bool;

    /**
     * Checks whether this collection has between $fromSize to $toSize items. $toSize can be
     * smaller than $fromSize.
     *
     * @param int $fromSize
     * @param int $toSize
     * @return bool
     */
    public function sizeIsBetween(int $fromSize, int $toSize): bool;

    /**
     * Checks whether this collection has more than $size items.
     *
     * @param int $size
     * @return bool
     */
    public function sizeIsGreaterThan(int $size): bool;

    /**
     * Checks whether this collection has less than $size items.
     *
     * @param int $size
     * @return bool
     */
    public function sizeIsLessThan(int $size): bool;

    /**
     * Returns lazy collection items of which are part of the original collection from item number $from to item
     * number $to. The items before $from are also iterated over, just not returned.
     *
     * @param int $from
     * @param int $to If omitted, will slice until end
     * @return self
     */
    public function slice(int $from, int $to = -1): self;

    /**
     * Returns true if $function returns true for at least one item in this collection, false otherwise.
     *
     * @param callable $some
     * @return bool
     */
    public function some(callable $some): bool;

    /**
     * Returns a non-lazy collection sorted using $function($item1, $item2, $key1, $key2 ). $function should
     * return true if first item is larger than the second and false otherwise.
     *
     * @param callable $sort ($value1, $value2, $key1, $key2)
     * @return self
     */
    public function sort(callable $sort): self;

    /**
     * Returns a collection of [take($position), drop($position)]
     *
     * @param int $position
     * @return self
     */
    public function splitAt(int $position): self;

    /**
     * Returns a collection of [takeWhile($predicament), dropWhile($predicament]
     *
     * @param callable $splitWith
     * @return self
     */
    public function splitWith(callable $splitWith): self;

    /**
     * Returns a sum of all values in this collection.
     *
     * @return int|float
     */
    public function sum(): float|int;

    /**
     * A form of slice that returns first $numberOfItems items.
     *
     * @param int $nItems
     * @return self
     */
    public function take(int $nItems): self;

    /**
     * Returns a lazy collection of every nth item in this collection
     *
     * @param int $step
     * @return self
     */
    public function takeNth(int $step): self;

    /**
     * Returns a lazy collection of items from the start of the ollection until the first item for which $function
     * returns false.
     *
     * @param callable $takeWhile
     * @return self
     */
    public function takeWhile(callable $takeWhile): self;

    /**
     * Converts $collection to array. If there are multiple items with the same key, only the last will be preserved.
     *
     * @param bool $onlyValues Shortcut for doing values()->toArray()
     * @return array
     */
    public function toArray(bool $onlyValues = false): array;

    /**
     * Converts collection and all children to array. If there are multiple items with the same key, only the last will be preserved.
     *
     * @param bool $onlyValues Shortcut for doing values()->toArrayRecursive()
     * @return array
     */
    public function toArrayRecursive(bool $onlyValues = false): array;

    /**
     * Returns a string by concatenating the collection values into a string.
     *
     * @return string
     */
    public function toString(): string;

    /**
     * Uses a $transformer callable that takes a Collection and returns Collection on itself.
     *
     * @param callable $transform Collection => Collection
     * @return self
     * @throws InvalidReturnValue
     */
    public function transform(callable $transform): self;

    /**
     * Transpose each item in a collection, interchanging the row and column indexes.
     * Can only transpose collections of collections. Otherwise, an InvalidArgument is raised.
     *
     * @param iterable ...$its
     * @return self
     */
    public function transpose(iterable ...$its): self;

    /**
     * Returns collection of values from this collection but with keys being numerical from 0 upwards.
     *
     * @return self
     */
    public function values(): self;

    /**
     * Returns a lazy collection of non-lazy collections of items from nth position from this collection and each
     * passed collection. Stops when any of the collections don't have an item at the nth position.
     *
     * @param iterable ...$its
     * @return self
     */
    public function zip(iterable ...$its): self;
}
