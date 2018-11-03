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

    public function getTestClass(): self
    {
        return $this->testClass;
    }
}
