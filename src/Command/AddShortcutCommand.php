<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Boot\MemoryInterface;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Spiral\Prototype\Shortcuts;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

final class AddShortcutCommand extends AbstractCommand
{
    public const NAME        = 'prototype:addShortcut';
    public const DESCRIPTION = 'Add a shortcut binding';
    public const ARGUMENTS   = [
        ['shortcut', InputArgument::REQUIRED, 'Shortcut name'],
        ['binding', InputArgument::REQUIRED, 'Shortcut binding'],
    ];
    public const OPTIONS     = [
        ['string', 's', InputOption::VALUE_OPTIONAL, 'Add binding using string, otherwise class const will be used (classname::class)', false]
    ];

    public function perform(Shortcuts\Validator $validator, MemoryInterface $memory): void
    {
        $shortcut = $this->input->getArgument('shortcut');
        $binding = '\\' . trim($this->input->getArgument('binding'), '\\');

        $errors = $validator->validate($shortcut, $binding);
        if (!empty($errors)) {
            $this->renderErrors(compact('shortcut', 'binding'), $errors);

            return;
        }

        $shortcuts = $this->getShortcuts($memory);

        if ($this->shortcutAlreadyDefined($shortcuts, $shortcut, $binding)) {
            $this->output->writeln("<comment>Shortcut `$shortcut:$binding` is already defined:</comment>");

            return;
        }

        if ($this->shortcutAlreadyBound($shortcuts, $shortcut, $binding)) {
            $this->output->writeln("<error>Shortcut `$shortcut` is already bound to {$shortcuts[$shortcut]}</error>");

            return;
        }

        if (!$this->isResolved($binding)) {
            $this->output->writeln("<error>Shortcut `$shortcut:$binding` is not resolved</error>");

            return;
        }

        try {
            $memory->saveData(PrototypeBootloader::MEMORY_SECTION, array_merge($shortcuts, [$shortcut => $binding]));
        } catch (\Throwable $e) {
            $this->sprintf(
                "<fg=red>%s [f: %s, l: %s]</fg=red>\n",
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
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

    private function getShortcuts(MemoryInterface $memory): array
    {
        $shortcuts = $memory->loadData(PrototypeBootloader::MEMORY_SECTION);
        if (empty($shortcuts) || !is_array($shortcuts)) {
            return [];
        }

        return $shortcuts;
    }

    private function shortcutAlreadyDefined(array $shortcuts, string $shortcut, string $binding): bool
    {
        if (!isset($shortcuts[$shortcut])) {
            return false;
        }

        return strcasecmp($shortcuts[$shortcut], $binding) === 0;
    }

    private function shortcutAlreadyBound($shortcuts, string $shortcut, string $binding): bool
    {
        if (!isset($shortcuts[$shortcut])) {
            return false;
        }

        return strcasecmp($shortcuts[$shortcut], $binding) !== 0;
    }

    private function isResolved(string $binding): bool
    {
        try {
            $this->container->get($binding);

            return true;
        } catch (ContainerExceptionInterface $e) {
        }

        try {
            $reflection = new \ReflectionClass($binding);

            return !$reflection->isAbstract() && !$reflection->isTrait() && !$reflection->isInterface();
        } catch (\Throwable $e) {
        }

        return false;
    }
}