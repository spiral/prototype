<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Tests\Fixtures;

use Spiral\Prototyping\Traits\PrototypeTrait;

class TestClass
{
    use PrototypeTrait;

    public function getSelf(): self
    {
        return $this->testClass;
    }

    public function testXX()
    {
        $this->testClass = 'hello';
    }
}