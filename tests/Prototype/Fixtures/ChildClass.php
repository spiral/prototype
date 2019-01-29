<?php

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Prototype\Traits\PrototypeTrait;

class ChildClass extends WithConstructor
{
    use PrototypeTrait;

    public function testMe()
    {
        return $this->testClass;
    }
}