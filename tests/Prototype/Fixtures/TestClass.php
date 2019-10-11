<?php

declare(strict_types=1);

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

    public function method(): void
    {
        $test2 = $this->test2;
        $test3 = $this->test3;
        $test = $this->test;
    }
}
