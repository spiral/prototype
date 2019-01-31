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

    public function method()
    {
        $test2 = $this->test2;
        $test3 = $this->test3;
        $test = $this->test;
    }
}