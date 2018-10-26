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
use Spiral\Prototyping\Tests\Fixtures\HydratedClass as X;

class TestClass
{
    use PrototypeTrait;

    /**
     * TestClass constructor.
     *
     * @param X $h
     *
     * @throws InvalidArgumentException
     */
    public function __construct(X $h)
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