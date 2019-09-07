<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Spiral\Prototype\Dependency;

final class ListCommand extends AbstractCommand
{
    public const NAME        = 'prototype:list';
    public const DESCRIPTION = 'List all prototyped classes';

    /**
     * List all prototype classes.
     */
    public function perform(): void
    {
        $prototyped = $this->locator->getTargetClasses();
        if ($prototyped === []) {
            $this->writeln('<comment>No prototyped classes found.</comment>');
            return;
        }

        $grid = $this->table(['Class:', 'Property:', 'Target:']);

        foreach ($prototyped as $class) {
            $proto = $this->getPrototypeProperties($class);

            $grid->addRow([$class->getName(), $this->mergeNames($proto), $this->mergeTargets($proto)]);
        }

        $grid->render();
    }

    /**
     * @param Dependency[] $properties
     * @return string
     */
    private function mergeNames(array $properties): string
    {
        return join("\n", array_keys($properties));
    }

    /**
     * @param Dependency[] $properties
     * @return string
     */
    private function mergeTargets(array $properties): string
    {
        $result = [];

        foreach (array_values($properties) as $target) {
            if ($target === null) {
                $result[] = '<fg=yellow>undefined</fg=yellow>';
                continue;
            }

            $result[] = $target->type->fullName;
        }

        return join("\n", $result);
    }
}