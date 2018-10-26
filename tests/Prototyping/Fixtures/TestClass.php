<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Tests\Fixtures;

use Spiral\Core\Exception\InvalidArgumentException;
use Spiral\Prototyping\Traits\PrototypeTrait;

class TestClass
{
    use PrototypeTrait;

    /**
     * TestClass constructor.
     *
     * @param H $h
     *
     * @throws InvalidArgumentException
     */
    public function __construct(H $h)
    {
    }

    public function getSelf(): self
    {
        return $this->testClass;
    }

    public function testXX()
    {
        $this->testClass = 'hello';
    }
}