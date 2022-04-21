<?php

namespace Nekman\Collection\Tests\Helpers;

class Car extends Machine
{
    public function __construct($name, private readonly int $numberOfSeats)
    {
        parent::__construct($name);
    }
}
