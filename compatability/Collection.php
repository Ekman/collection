<?php

namespace DusanKasan\Knapsack;

use ArrayIterator;
use DusanKasan\Knapsack\Exceptions\InvalidArgument;
use DusanKasan\Knapsack\Exceptions\InvalidReturnValue;
use IteratorAggregate;
use Serializable;
use Traversable;
use function Nekman\Collection\iterable_reference_key_value;
use function Nekman\Collection\iterable_to_array;

/**
 * @deprecated
 * @see \Nekman\Collection\Collection
 */
class Collection implements IteratorAggregate, Serializable
{
    use CollectionTrait;

    protected iterable $input;

    /**
     * @param callable|iterable $input If callable is passed, it must return an array|Traversable.
     */
    public function __construct(callable|iterable $input)
    {
        if (is_callable($input)) {
            $input = $input();
        }

        if (is_array($input)) {
            $this->input = new ArrayIterator($input);
        } elseif ($input instanceof Traversable) {
            $this->input = $input;
        } else {
            throw is_callable($input) ? new InvalidReturnValue : new InvalidArgument;
        }
    }

    /**
     * Static alias of normal constructor.
     *
     * @param callable|iterable $input
     * @return Collection
     */
    public static function from(callable|iterable $input)
    {
        return new self($input);
    }

    /**
     * Returns lazy collection of values, where first value is $input and all subsequent values are computed by applying
     * $function to the last value in the collection. By default this produces an infinite collection. However you can
     * end the collection by throwing a NoMoreItems exception.
     *
     * @param mixed $input
     * @param callable $function
     * @return Collection
     */
    public static function iterate(mixed $input, callable $function)
    {
        return iterate($input, $function);
    }

    /**
     * Returns a lazy collection of numbers starting at $start, incremented by $step until $end is reached.
     *
     * @param int $start
     * @param int|null $end
     * @param int $step
     * @return Collection
     */
    public static function range(int $start = 0, ?int $end = null, int $step = 1)
    {
        return range($start, $end, $step);
    }

    /**
     * Returns a lazy collection of $value repeated $times times. If $times is not provided the collection is infinite.
     *
     * @param mixed $value
     * @param int $times
     * @return Collection
     */
    public static function repeat($value, $times = -1)
    {
        return repeat($value, $times);
    }

    public function getIterator(): Traversable
    {
        return $this->input;
    }

    public function serialize(): string
    {
        return serialize($this->__serialize());
    }

    public function __serialize(): array
    {
        return iterable_to_array(
            iterable_reference_key_value($this->input)
        );
    }

    public function unserialize(string $data): void
    {
        $this->__unserialize(unserialize($data));
    }

    public function __unserialize(array $serialized): void
    {
        $this->input = dereferenceKeyValue($serialized);
    }
}
