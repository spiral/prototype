<?php
declare(strict_types=1);
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Bootloader;

use Spiral\Config\ConfiguratorInterface;
use Spiral\Config\Patch\AppendPatch;
use Spiral\Core\Bootloader\Bootloader;
use Spiral\Prototype\Command\InjectCommand;
use Spiral\Prototype\Command\ListCommand;

class PrototypeBootloader extends Bootloader
{
    const BOOT = true;

    /**
     * @param ConfiguratorInterface $configurator
     */
    public function boot(ConfiguratorInterface $configurator)
    {
        $configurator->modify(
            'console',
            new AppendPatch('commands', null, ListCommand::class)
        );
        $configurator->modify(
            'console',
            new AppendPatch('commands', null, InjectCommand::class)
        );
    }
}