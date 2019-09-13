<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Boot\DirectoriesInterface;
use Spiral\Files\Files;
use Spiral\Files\FilesInterface;
use Spiral\Framework\Kernel;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Spiral\Prototype\PrototypeRegistry;
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures;
use Spiral\Prototype\Tests\Commands\Fixtures\InterfaceResolver;
use Spiral\Prototype\Tests\Commands\Fixtures\ResolvedInterface;

class TestApp extends Kernel
{
    const LOAD = [
        PrototypeBootloader::class
    ];

    public function bindApp(): void
    {
        $this->bindWithoutResolver();
        $this->container->bind(Fixtures\ATest3Interface::class, Fixtures\ATest3::class);
        $this->container->bind(ResolvedInterface::class, InterfaceResolver::class);
        $this->container->bind(FilesInterface::class, Files::class);
    }

    public function bindWithoutResolver(): void
    {
        /** @var PrototypeRegistry $registry */
        $registry = $this->container->get(PrototypeRegistry::class);

        $registry->bindProperty('testClass', self::class);
        $registry->bindProperty('test', Fixtures\Test::class);
        $registry->bindProperty('test2', Fixtures\SubFolder\Test::class);
        $registry->bindProperty('test3', Fixtures\ATest3Interface::class);
    }

    public function get(string $target)
    {
        return $this->container->get($target);
    }
}
