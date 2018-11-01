<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Bootloader;


use Spiral\Console\ConsoleConfigurator;
use Spiral\Core\Bootloader\Bootloader;
use Spiral\Prototyping\Command\InjectCommand;
use Spiral\Prototyping\Command\ListCommand;

class PrototypingBootloader extends Bootloader
{
    const BOOT = true;

    /**
     * @param ConsoleConfigurator $console
     */
    public function boot(ConsoleConfigurator $console)
    {
        $console->addCommand(ListCommand::class);
        $console->addCommand(InjectCommand::class);
    }
}