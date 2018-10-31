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
    use PrototypeTrait, XTrait;

    public $reddit;

    private $magic;

    public function getSelf(): self
    {
        return $this->testClass;
    }

    public function getSelf2(): self
    {
        return $this->testClass;
    }
}