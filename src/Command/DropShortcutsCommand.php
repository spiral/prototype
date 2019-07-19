<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Spiral\Prototype\Shortcuts;

final class DropShortcutsCommand extends AbstractCommand
{
    public const NAME        = 'prototype:dropShortcuts';
    public const DESCRIPTION = 'Drop all shortcut bindings';

    public function perform(Shortcuts\Injector $injector): void
    {
        $shortcuts = $injector->drop();
        $count = count($shortcuts);

        $this->output->writeln("<comment>{$count} Shortcut(s) were dropped.</comment>");
    }
}