<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Command;

use Spiral\Prototype\Injector;

class InjectCommand extends AbstractCommand
{
    const NAME        = "prototype:inject";
    const DESCRIPTION = "Inject all prototype dependencies";

    /**
     * Perform command.
     */
    public function perform()
    {
        $targets = $this->getTargets();
        if (empty($targets)) {
            $this->writeln("<comment>No prototyped classes found.</comment>");

            return;
        }

        $injector = new Injector();
        foreach ($targets as $class) {
            $deps = $this->fetchDependencies($class);

            foreach ($deps as $dep) {
                if ($dep instanceof \Throwable) {
                    $this->sprintf(
                        "<fg=red>•</fg=red> %s: <fg=red>%s</fg=red>",
                        $class->getName(),
                        $dep->getMessage()
                    );

                    continue 2;
                }
            }

            $this->sprintf(
                "<fg=green>•</fg=green> %s: injecting <fg=green>%s</fg=green>",
                $class->getName(),
                join("</fg=green>, <fg=green>", array_values($deps))
            );


            $modified = $injector->injectDependencies(
                file_get_contents($class->getFileName()),
                $deps
            );

            file_put_contents($class->getFileName(), $modified);
        }
    }
}