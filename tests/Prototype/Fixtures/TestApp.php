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

class TestApp extends Kernel
{
    const LOAD = [
        ConsoleBootloader::class,
        PrototypeBootloader::class
    ];

    public function bindApp()
    {
        $this->container->bind('testClass', self::class);
    }

    public function get(string $target)
    {
        return $this->container->get($target);
    }
}