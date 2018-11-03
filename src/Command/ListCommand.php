<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Command;

class ListCommand extends AbstractCommand
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
            $deps = $this->fetchDependencies($class);
            $grid->addRow([
                $class->getName(),
                join("\n", array_keys($deps)),
                $this->mergeValues(array_values($deps))
            ]);
        }

        $grid->render();
    }

    /**
     * @param array $deps
     * @return string
     */
    private function mergeValues(array $deps): string
    {
        $result = [];

        foreach ($deps as $dep) {
            if ($dep instanceof \Throwable) {
                $result[] = sprintf("<fg=red>%s</fg=red>", $dep->getMessage());
                continue;
            }

            if (is_null($dep)) {
                $result[] = "<fg=yellow>undefined</fg=yellow>";
                continue;
            }

            $result[] = $dep;
        }

        return join("\n", $result);
    }
}