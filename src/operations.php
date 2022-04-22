<?php

namespace Nekman\Collection;

use Iterator;
use IteratorIterator;
use Nekman\Collection\Exceptions\InvalidArgument;
use Nekman\Collection\Exceptions\InvalidReturnValue;
use Nekman\Collection\Exceptions\ItemNotFound;
use ReflectionObject;
use Traversable;
use function shuffle;

function iterable_append(iterable $it, mixed $value, mixed $key = null): iterable
{
    foreach ($it as $k => $v) {
        yield $k => $v;
    }

    if ($key === null) {
        yield $value;
    } else {
        yield $key => $value;
    }
}

function iterable_average(iterable $it): int|float
{
    $sum = 0;
    $count = 0;

    foreach ($it as $value) {
        $sum += $value;
        $count++;
    }

    return $count ? $sum / $count : 0;
}

function iterable_combine(iterable $keys, iterable $values): iterable
{
    $valueIt = new IteratorIterator(iterable_to_traversable($values));
    $valueIt->rewind();

    foreach ($keys as $key) {
        if (!$valueIt->valid()) {
            break;
        }

        yield $key => $valueIt->current();
        $valueIt->next();
    }
}

function iterable_concat(iterable ...$its): iterable
{
    foreach ($its as $it) {
        yield from $it;
    }
}

function iterable_contains(iterable $it, mixed $needle): bool
{
    foreach ($it as $value) {
        if ($value === $needle) {
            return true;
        }
    }

    return false;
}

function iterable_count_by(iterable $it, callable $countBy): iterable
{
    return iterable_map(
        iterable_group_by($it, $countBy),
        "iterable_size"
    );
}

function iterable_cycle(iterable $it): iterable
{
    while (true) {
        yield from $it;
    }
}

function iterable_diff(iterable $it, iterable ...$its): iterable
{
    $valuesToCompare = iterable_to_array(
        iterable_concat(...$its),
        true
    );

    foreach ($it as $key => $value) {
        if (!in_array($value, $valuesToCompare)) {
            yield $key => $value;
        }
    }
}

function iterable_distinct(iterable $it): iterable
{
    $distinctValues = [];

    foreach ($it as $key => $value) {
        if (!in_array($value, $distinctValues)) {
            $distinctValues[] = $value;
            yield $key => $value;
        }
    }
}

function iterable_drop(iterable $it, int $nItems): iterable
{
    return iterable_slice($it, $nItems);
}

function iterable_drop_last(iterable $it, int $nItems = 1): iterable
{
    $buffer = [];

    foreach ($it as $key => $value) {
        $buffer[] = [$key, $value];

        if (count($buffer) > $nItems) {
            $val = array_shift($buffer);
            yield $val[0] => $val[1];
        }
    }
}

function iterable_drop_while(iterable $it, callable $dropWhile): iterable
{
    $shouldDrop = true;
    foreach ($it as $key => $value) {
        if ($shouldDrop) {
            $shouldDrop = $dropWhile($value, $key);
        }

        if (!$shouldDrop) {
            yield $key => $value;
        }
    }
}

function iterable_each(iterable $it, callable $each): iterable
{
    foreach ($it as $key => $value) {
        $each($value, $key);
    }

    return $it;
}

function iterable_every(iterable $it, callable $every): bool
{
    foreach ($it as $key => $value) {
        if (!$every($value, $key)) {
            return false;
        }
    }

    return true;
}

function iterable_extract(iterable $it, string $keyPath): iterable
{
    preg_match_all('/(.*[^\\\])(?:\.|$)/U', $keyPath, $matches);
    $pathParts = $matches[1];

    $extractor = function ($coll) use ($pathParts) {
        foreach ($pathParts as $pathPart) {
            $coll = iterable_flatten(iterable_filter($coll, 'is_iterable'), 1);

            if ($pathPart != '*') {
                $pathPart = str_replace(['\.', '\*'], ['.', '*'], $pathPart);
                $coll = iterable_values(iterable_only($coll, [$pathPart]));
            }
        }

        return $coll;
    };

    foreach ($it as $item) {
        foreach ($extractor([$item]) as $extracted) {
            yield $extracted;
        }
    }
}

function iterable_filter(iterable $it, ?callable $filter = null): iterable
{
    if (!$filter) {
        $filter = "boolval";
    }

    foreach ($it as $key => $value) {
        if ($filter($value, $key)) {
            yield $key => $value;
        }
    }
}

function iterable_find(iterable $it, callable $find, mixed $default = nulle): mixed
{
    foreach ($it as $key => $value) {
        if ($find($value, $key)) {
            return $value;
        }
    }

    return $default;
}

function iterable_first(iterable $it, bool $convertToIterable = false): mixed
{
    foreach ($it as $value) {
        return $convertToIterable ? [$value] : $value;
    }

    throw new ItemNotFound;
}

function iterable_index_by(iterable $it, callable $indexBy): iterable
{
    foreach ($it as $key => $value) {
        yield $indexBy($value, $key) => $value;
    }
}

function iterable_except(iterable $it, iterable $keys): iterable
{
    $keys = iterable_to_array($keys, true);

    return iterable_reject(
        $it,
        fn ($value, $key) => in_array($key, $keys)
    );
}

function iterable_reject(iterable $it, callable $reject): iterable
{
    return iterable_filter(
        $it,
        fn ($value, $key) => !$reject($value, $key)
    );
}

function iterable_slice(iterable $it, int $from, int $to = -1): iterable
{
    $index = 0;

    foreach ($it as $key => $value) {
        if ($index >= $from && ($index < $to || $to == -1)) {
            yield $key => $value;
        } elseif ($index >= $to && $to >= 0) {
            break;
        }

        $index++;
    }
}

function iterable_values(iterable $it): iterable
{
    foreach ($it as $value) {
        yield $value;
    }
}

function iterable_map(iterable $it, callable $map): iterable
{
    foreach ($it as $key => $value) {
        yield $key => $map($value, $key);
    }
}

function iterable_reduce(iterable $it, callable $reduce, mixed $startValue): mixed
{
    foreach ($it as $key => $value) {
        $startValue = $reduce($startValue, $value, $key);
    }

    return $startValue;
}

function iterable_size(iterable $it): int
{
    return is_countable($it)
        ? count($it)
        : iterator_count(
            iterable_to_traversable($it)
        );
}

function iterable_size_is_between(iterable $it, int $fromSize, int $toSize): bool
{
    if ($fromSize > $toSize) {
        $tmp = $toSize;
        $toSize = $fromSize;
        $fromSize = $tmp;
    }

    $itemsTempCount = 0;
    foreach ($it as $value) {
        $itemsTempCount++;

        if ($itemsTempCount > $toSize) {
            return false;
        }
    }

    return $fromSize < $itemsTempCount && $itemsTempCount < $toSize;
}

function iterable_size_is(iterable $it, int $size): bool
{
    $itemsTempCount = 0;

    foreach ($it as $value) {
        $itemsTempCount++;

        if ($itemsTempCount > $size) {
            return false;
        }
    }

    return $itemsTempCount == $size;
}

function iterable_size_is_greater_than(iterable $it, int $size): bool
{
    $itemsTempCount = 0;

    foreach ($it as $value) {
        $itemsTempCount++;

        if ($itemsTempCount > $size) {
            return true;
        }
    }

    return $itemsTempCount > $size;
}

function iterable_size_is_less_than(iterable $it, int $size): bool
{
    $itemsTempCount = 0;

    foreach ($it as $value) {
        $itemsTempCount++;

        if ($itemsTempCount > $size) {
            return false;
        }
    }

    return $itemsTempCount < $size;
}

function iterable_sum(iterable $it): float|int
{
    return iterable_reduce(
        $it,
        fn (float|int $sum, float|int $number) => $sum + $number,
        0
    );
}

function iterable_to_array(iterable $it, bool $onlyValues = false): array
{
    if ($onlyValues) {
        $it = iterable_values($it);
    } elseif (is_array($it)) {
        return $it;
    }

    return iterator_to_array(
        iterable_to_traversable($it)
    );
}

function iterable_to_array_recursive(iterable $it, bool $onlyValues = false): array
{
    if ($onlyValues) {
        $it = iterable_values($it);
    }

    $array = [];

    foreach ($it as $key => $value) {
        if (is_iterable($value)) {
            $value = iterable_to_array_recursive($value, $onlyValues);
        }

        $array[$key] = $value;
    }

    return $array;
}

function iterable_realize(iterable $it): iterable
{
    return iterable_to_array($it);
}

function iterable_to_traversable(iterable $it): Traversable
{
    yield from $it;
}

function iterable_keys(iterable $it): iterable
{
    foreach ($it as $key => $value) {
        yield $key;
    }
}

function iterable_flatten(iterable $it, int $levelsToFlatten = -1): iterable
{
    $flattenNextLevel = $levelsToFlatten < 0 || $levelsToFlatten > 0;
    $childLevelsToFlatten = $levelsToFlatten > 0 ? $levelsToFlatten - 1 : $levelsToFlatten;

    foreach ($it as $key => $value) {
        if ($flattenNextLevel && is_iterable($value)) {
            foreach (iterable_flatten($value, $childLevelsToFlatten) as $childKey => $childValue) {
                yield $childKey => $childValue;
            }
        } else {
            yield $key => $value;
        }
    }
}

function iterable_mapcat(iterable $it, callable $mapcat): iterable
{
    return iterable_flatten(
        iterable_map($it, $mapcat),
        1
    );
}

function iterable_only(iterable $it, iterable $keys): iterable
{
    $keys = iterable_to_array($keys, true);

    return iterable_filter(
        $it,
        fn ($value, $key) => in_array($key, $keys, true)
    );
}

function iterable_flip(iterable $it): iterable
{
    foreach ($it as $key => $value) {
        yield $value => $key;
    }
}

function iterable_get(iterable $it, mixed $key): mixed
{
    foreach ($it as $valueKey => $value) {
        if ($valueKey === $key) {
            return $value;
        }
    }

    throw new ItemNotFound;
}

function iterable_get_or_default(iterable $it, mixed $key, mixed $default = null): mixed
{
    try {
        return iterable_get($it, $key);
    } catch (ItemNotFound) {
        return $default;
    }
}

function iterable_group_by(iterable $it, callable $groupBy): iterable
{
    $groups = [];

    foreach ($it as $key => $value) {
        $groupKey = $groupBy($value, $key);
        $groups[$groupKey][] = $value;
    }

    return $groups;
}

function iterable_group_by_key(iterable $it, mixed $key): iterable
{
    return iterable_group_by(
        iterable_filter(
            $it,
            fn ($item) => iterable_has($item, $key),
        ),
        fn ($value) => iterable_get($value, $key)
    );
}

function iterable_has(iterable $it, mixed $key): bool
{
    try {
        iterable_get($it, $key);
        return true;
    } catch (ItemNotFound) {
        return false;
    }
}

function iterable_frequencies(iterable $it): iterable
{
    return iterable_count_by($it, '\Nekman\Collection\identity');
}

function iterable_interleave(iterable ...$its): iterable
{
    /* @var Iterator[] $iterators */
    $iterators = iterable_map(
        $its,
        function (iterable $it) {
            $it = new IteratorIterator(iterable_to_traversable($it));
            $it->rewind();
            return $it;
        }
    );

    $valid = false;

    do {
        foreach ($iterators as $it) {
            if ($valid = $it->valid()) {
                yield $it->key() => $it->current();
                $it->next();
            }
        }
    } while ($valid);
}

function iterable_take(iterable $it, int $nItems): iterable
{
    return iterable_slice($it, 0, $nItems);
}

function iterable_interpose(iterable $it, mixed $separator): iterable
{
    foreach (iterable_take($it, 1) as $key => $value) {
        yield $key => $value;
    }

    foreach (iterable_drop($it, 1) as $key => $value) {
        yield $separator;
        yield $key => $value;
    }
}

function iterable_intersect(iterable $it, iterable ...$its): iterable
{
    $valuesToCompare = iterable_to_array(
        iterable_concat(...$its),
        true
    );

    foreach ($it as $key => $value) {
        if (in_array($value, $valuesToCompare)) {
            yield $key => $value;
        }
    }
}

function iterable_is_empty(iterable $it): bool
{
    foreach ($it as $value) {
        return false;
    }

    return true;
}

function iterable_is_not_empty(iterable $it): bool
{
    return !iterable_is_empty($it);
}

function iterable_last(iterable $it, bool $convertToIterable = false): mixed
{
    foreach ($it as $value) {
        $last = $value;
    }

    if (!isset($last)) {
        throw new ItemNotFound;
    }

    return $convertToIterable ? [$last] : $last;
}

function iterable_max(iterable $it): mixed
{
    $max = PHP_INT_MIN;

    foreach ($it as $value) {
        $max = max($value, $max);
    }

    return $max === PHP_INT_MIN ? null : $max;
}

function iterable_min(iterable $it): mixed
{
    $min = PHP_INT_MAX;

    foreach ($it as $value) {
        $min = min($value, $min);
    }

    return $min === PHP_INT_MAX ? null : $min;
}

function iterable_partition(iterable $it, int $nItems, int $step = 0, iterable $padding = []): iterable
{
    $buffer = [];
    $itemsToSkip = 0;
    $tmpStep = $step ?: $nItems;

    foreach ($it as $key => $value) {
        if (count($buffer) == $nItems) {
            yield $buffer;

            $buffer = array_slice($buffer, $tmpStep);
            $itemsToSkip = $tmpStep - $nItems;
        }

        if ($itemsToSkip <= 0) {
            $buffer[$key] = $value;
        } else {
            $itemsToSkip--;
        }
    }

    yield iterable_take(
        iterable_concat($buffer, $padding),
        $nItems
    );
}

function iterable_partition_by(iterable $it, callable $partitionBy): iterable
{
    $result = null;
    $buffer = [];

    foreach ($it as $key => $value) {
        $newResult = $partitionBy($value, $key);

        if (!empty($buffer) && $result != $newResult) {
            yield $buffer;
            $buffer = [];
        }

        $result = $newResult;
        $buffer[$key] = $value;
    }

    if (!empty($buffer)) {
        yield $buffer;
    }
}

function iterable_prepend(iterable $it, mixed $value, mixed $key = null): iterable
{
    if ($key) {
        yield $key => $value;
    } else {
        yield $value;
    }

    foreach ($it as $k => $v) {
        yield $k => $v;
    }
}

function iterable_reduce_right(iterable $it, callable $reduceRight, mixed $startValue): mixed
{
    return iterable_reduce(iterable_reverse($it), $reduceRight, $startValue);
}

function iterable_reverse(iterable $it): iterable
{
    return array_reverse(iterable_to_array($it), true);
}

function iterable_reductions(iterable $it, callable $reductions, mixed $startValue): iterable
{
    yield $startValue;

    foreach ($it as $key => $value) {
        yield $startValue = $reductions($startValue, $value, $key);
    }
}

function iterable_join(iterable $it, string $separator = ""): string
{
    $joined = iterable_reduce(
        $it,
        fn (string $joined, string $string) => $joined . $separator . $string,
        ""
    );

    return ltrim($joined, $separator);
}

function iterable_sort(iterable $it, callable $sort): iterable
{
    $array = iterable_to_array(
        iterable_reference_key_value($it),
        true
    );

    uasort(
        $array,
        fn ($a, $b) => $sort($a[1], $b[1], $a[0], $b[0])
    );

    return iterable_dereference_key_value($array);
}

function iterable_dereference_key_value(iterable $it): iterable
{
    foreach ($it as $value) {
        yield $value[0] => $value[1];
    }
}

function iterable_reference_key_value(iterable $it): iterable
{
    foreach ($it as $key => $value) {
        yield [$key, $value];
    }
}

function iterable_to_string(iterable $it): string
{
    return iterable_reduce(
        $it,
        fn (string $sum, string $value) => $sum . $value,
        ""
    );
}

function iterable_zip(iterable ...$its): iterable
{
    /* @var Iterator[] $iterators */
    $iterators = iterable_map(
        $its,
        function ($it) {
            $it = new IteratorIterator(iterable_to_traversable($it));
            $it->rewind();
            return $it;
        }
    );

    while (true) {
        $isMissingItems = false;
        $zippedItem = [];

        foreach ($iterators as $it) {
            if (!$it->valid()) {
                $isMissingItems = true;
                break;
            }

            $zippedItem[$it->key()] = $it->current();
            $it->next();
        }

        if (!$isMissingItems) {
            yield $zippedItem;
        } else {
            break;
        }
    }
}

function iterable_some(iterable $it, callable $some): bool
{
    foreach ($it as $key => $value) {
        if ($some($value, $key)) {
            return true;
        }
    }

    return false;
}

function iterable_transpose(iterable ...$its): iterable
{
    if (iterable_some($its, fn ($value) => !is_iterable($value))) {
        throw new InvalidArgument('Can only transpose iterable of iterables.');
    }

    return iterable_map(
        $its,
        'Nekman\Collection\iterable_to_array'
    );
}

function iterable_transform(iterable $it, callable $transform): iterable
{
    $transformed = $transform($it);

    if (!is_iterable($transformed)) {
        throw new InvalidReturnValue;
    }

    return $transformed;
}

function iterable_take_while(iterable $it, callable $takeWhile): iterable
{
    $shouldTake = true;

    foreach ($it as $key => $value) {
        if ($shouldTake) {
            $shouldTake = $takeWhile($value, $key);
        }

        if ($shouldTake) {
            yield $key => $value;
        }
    }
}

function iterable_take_nth(iterable $it, int $step): iterable
{
    $index = 0;

    foreach ($it as $key => $value) {
        if ($index % $step == 0) {
            yield $key => $value;
        }

        $index++;
    }
}

function iterable_split_at(iterable $it, int $position): iterable
{
    yield iterable_take($it, $position);
    yield iterable_drop($it, $position);
}

function iterable_split_with(iterable $it, callable $splitWith): iterable
{
    yield iterable_take_while($it, $splitWith);
    yield iterable_drop_while($it, $splitWith);
}

function dump(mixed $input, ?int $maxItemsPerCollection = null, ?int $maxDepth = null)
{
    if (is_scalar($input)) {
        return $input;
    }

    if (is_array($input) || $input instanceof Traversable) {
        if ($maxDepth === 0) {
            return '^^^';
        }

        $normalizedProperties = [];
        foreach ($input as $key => $value) {
            if ($maxItemsPerCollection !== null && count($normalizedProperties) >= $maxItemsPerCollection) {
                $normalizedProperties[] = '>>>';
                break;
            }

            for ($affix = 0; true; $affix++) {
                $betterKey = $affix ? "$key//$affix" : $key;
                if (!array_key_exists($betterKey, $normalizedProperties)) {
                    $normalizedProperties[$betterKey] = dump(
                        $value,
                        $maxItemsPerCollection,
                        $maxDepth > 0 ? $maxDepth - 1 : null
                    );

                    break;
                }
            }
        }

        return $normalizedProperties;
    }

    if (is_object($input)) {
        if ($maxDepth === 0) {
            return '^^^';
        }

        $reflection = new ReflectionObject($input);
        $normalizedProperties = [];
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $normalizedProperties[$property->getName()] = $property->getValue($input);
        }
        return [get_class($input) => dump($normalizedProperties, null, $maxDepth > 0 ? $maxDepth - 1 : null)];
    }

    return gettype($input);
}

function print_dump(mixed $input, ?int $maxItemsPerCollection = null, ?int $maxDepth = null)
{
    var_export(dump($input, $maxItemsPerCollection, $maxDepth));
    return $input;
}

function iterable_replace(iterable $it, iterable $replace): iterable
{
    foreach ($it as $key => $value) {
        $newValue = iterable_get_or_default($replace, $value, $value);
        yield $key => $newValue;
    }
}

function iterable_replace_by_keys(iterable $it, iterable $replace): iterable
{
    foreach ($it as $key => $value) {
        $newValue = iterable_get_or_default($replace, $key, $value);
        yield $key => $newValue;
    }
}

function iterable_second(iterable $it): mixed
{
    return iterable_get(iterable_values($it), 1);
}

function iterable_shuffle(iterable $it): iterable
{
    $buffer = iterable_to_array(
        iterable_reference_key_value($it),
        true
    );

    shuffle($buffer);

    return iterable_dereference_key_value($buffer);
}

function duplicate(mixed $input): mixed
{
    if (is_array($input)) {
        return array_map(fn ($i) => duplicate($i), $input);
    }

    if (is_object($input)) {
        return clone $input;
    }

    return $input;
}
