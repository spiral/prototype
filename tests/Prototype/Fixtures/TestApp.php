<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Framework\Kernel;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures;

class TestApp extends Kernel
{
    const LOAD = [
        PrototypeBootloader::class
    ];

    public function bindApp(): void
    {
        $this->bindWithoutResolver();
        $this->container->bind(Fixtures\ATest3Interface::class, Fixtures\ATest3::class);
    }

    public function bindWithoutResolver(): void
    {
        $this->container->bind('testClass', self::class);
        $this->container->bind('test', Fixtures\Test::class);
        $this->container->bind('test2', Fixtures\SubFolder\Test::class);
        $this->container->bind('test3', Fixtures\ATest3Interface::class);
    }

    public function get(string $target)
    {
        return $this->container->get($target);
    }
}