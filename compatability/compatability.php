<?php


use Nekman\Collection\Collection;
use Nekman\Collection\Exceptions\CollectionException;
use Nekman\Collection\Exceptions\InvalidArgument;
use Nekman\Collection\Exceptions\InvalidReturnValue;
use Nekman\Collection\Exceptions\ItemNotFound;
use Nekman\Collection\Exceptions\NoMoreItems;

class_alias(CollectionException::class, "\\DusanKasan\\Knapsack\\Exceptions\\RuntimeException");
class_alias(Collection::class, "\\DusanKasan\\Knapsack\\Collection");
class_alias(InvalidArgument::class, "\\DusanKasan\\Knapsack\\Exceptions\\InvalidArgument");
class_alias(InvalidReturnValue::class, "\\DusanKasan\\Knapsack\\Exceptions\\InvalidReturnValue");
class_alias(ItemNotFound::class, "\\DusanKasan\\Knapsack\\Exceptions\\ItemNotFound");
class_alias(NoMoreItems::class, "\\DusanKasan\\Knapsack\\Exceptions\\NoMoreItems");
