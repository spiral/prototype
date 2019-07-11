<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Spiral\Boot\MemoryInterface;
use Spiral\Prototype\Shortcuts;
use Symfony\Component\Console\Input\InputArgument;

final class AddShortcutCommand extends AbstractCommand
{
    public const NAME        = 'prototype:addShortcut';
    public const DESCRIPTION = 'Add a shortcut binding';
    public const ARGUMENTS   = [
        ['shortcut', InputArgument::REQUIRED, 'Shortcut name'],
        ['binding', InputArgument::REQUIRED, 'Shortcut binding'],
    ];

    public function perform(Shortcuts\Validator $validator, MemoryInterface $memory, Shortcuts\Injector $injector): void
    {
        $shortcut = $this->input->getArgument('shortcut');
        $binding = trim($this->input->getArgument('binding'), '\\');

        $errors = $validator->validate($shortcut, $binding);
        if (!empty($errors)) {
            $this->renderErrors(compact('shortcut', 'binding'), $errors);

            return;
        }

        $result = $injector->inject($shortcut, $binding);
        if ($result->defined) {
            $this->output->writeln("<comment>Shortcut `$shortcut:$binding` is already defined:</comment>");

            return;
        }

        if ($result->bound) {
            $this->output->writeln("<error>Shortcut `$shortcut` is already bound to {$result->boundTo}</error>");

            return;
        }

        if (!$result->resolved) {
            $this->output->writeln("<error>Shortcut `$shortcut:$binding` is not resolved</error>");

            return;
        }

        $this->output->writeln("<comment>Shortcut `$shortcut:$binding` successfully added</comment>");
    }

    private function renderErrors(array $arguments, array $errors): void
    {
        $this->output->writeln('<error>Input contains next error(s):</error>');
        $grid = $this->table(['Argument:', 'Value:', 'Error:']);

        foreach ($errors as $argument => $error) {
            $grid->addRow([
                $argument,
                $arguments[$argument],
                $error
            ]);
        }

        $grid->render();
    }
}