<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Bootloader;

use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\Bootloader\DependedInterface;
use Spiral\Bootloader\ConsoleBootloader;
use Spiral\Prototype\Command\InjectCommand;
use Spiral\Prototype\Command\ListCommand;

final class PrototypeBootloader extends Bootloader implements DependedInterface
{
    /**
     * @param ConsoleBootloader $console
     */
    public function boot(ConsoleBootloader $console)
    {
        $console->addCommand(ListCommand::class);
        $console->addCommand(InjectCommand::class);
    }

    /**
     * @return array
     */
    public function defineDependencies(): array
    {
        return [
            ConsoleBootloader::class
        ];
    }
}