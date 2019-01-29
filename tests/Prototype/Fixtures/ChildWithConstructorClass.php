<?php

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Prototype\Traits\PrototypeTrait;

class ChildWithConstructorClass extends WithConstructor
{
    use PrototypeTrait;

    public function __construct()
    {
    }

    public function testMe()
    {
        return $this->testClass;
    }
}