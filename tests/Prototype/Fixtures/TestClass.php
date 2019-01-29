<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Prototype\Traits\PrototypeTrait;

class TestClass
{
    use PrototypeTrait;

    public function getTest()
    {
        return $this->testClass;
    }

    public function method()
    {
        $test2 = $this->test2;
        $test = $this->test;
    }
}
