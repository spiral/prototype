<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Command;

use Psr\Container\ContainerInterface;
use Spiral\Tokenizer\ClassesInterface;

class ListCommand extends AbstractCommand
{
    const NAME        = "prototype:list";
    const DESCRIPTION = "List all prototyped classes";

    /**
     * @param ClassesInterface   $classes
     * @param ContainerInterface $container
     */
    public function perform(ClassesInterface $classes, ContainerInterface $container)
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
                join("\n", array_values($deps)),
            ]);
        }

        $grid->render();
    }
}