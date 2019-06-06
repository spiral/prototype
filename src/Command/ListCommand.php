<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

final class ListCommand extends AbstractCommand
{
    const NAME        = "prototype:list";
    const DESCRIPTION = "List all prototyped classes";

    /**
     * List all prototype classes.
     */
    public function perform()
    {
        $targets = $this->getTargets();
        if (empty($targets)) {
            $this->writeln("<comment>No prototyped classes found.</comment>");

            return;
        }

        $grid = $this->table(['Class:', 'Dependencies:', 'Resolution:']);

        foreach ($targets as $class) {
            $dependencies = $this->fetchDependencies($class);
            $grid->addRow([
                $class->getName(),
                join("\n", array_keys($dependencies)),
                $this->mergeValues($dependencies)
            ]);
        }

        $grid->render();
    }

    /**
     * @param \Spiral\Prototype\Dependency[] $dependencies
     * @return string
     */
    private function mergeValues(array $dependencies): string
    {
        $result = [];

        foreach ($dependencies as $dependency) {
            if ($dependency instanceof \Throwable) {
                $result[] = sprintf("<fg=red>%s</fg=red>", $dependency->getMessage());
                continue;
            }

            if (is_null($dependency)) {
                $result[] = "<fg=yellow>undefined</fg=yellow>";
                continue;
            }

            $result[] = $dependency->type->fullName;
        }

        return join("\n", $result);
    }
}