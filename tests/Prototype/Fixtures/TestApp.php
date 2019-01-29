<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Bootloader\Dispatcher\ConsoleBootloader;
use Spiral\Core\Kernel;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures\Test;

class TestApp extends Kernel
{
    const LOAD = [
        ConsoleBootloader::class,
        PrototypeBootloader::class
    ];

    public function bindApp()
    {
        $this->container->bind('testClass', self::class);
        $this->container->bind('test', Test::class);
        $this->container->bind('test2', Test::class);
    }

    public function get(string $target)
    {
        return $this->container->get($target);
    }
}