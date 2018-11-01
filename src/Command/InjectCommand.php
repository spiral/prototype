<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Command;


use Psr\Container\ContainerInterface;
use Spiral\Prototyping\Injector;
use Spiral\Tokenizer\ClassesInterface;

class InjectCommand extends AbstractCommand
{
    const NAME        = "prototype:inject";
    const DESCRIPTION = "Inject all prototype dependencies";

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


            $source = $injector->injectDependencies(file_get_contents($class->getFileName()), $deps);
            file_put_contents($class->getFileName(), $source);
        }
    }
}