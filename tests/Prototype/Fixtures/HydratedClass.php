<?php

declare(strict_types=1);

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

class HydratedClass
{
    private $testClass;

    public function __construct(TestClass $t)
    {
        $this->testClass = $t;
    }

    public function getTestClass(): TestClass
    {
        return $this->testClass;
    }
}
