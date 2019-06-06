<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Spiral\Prototype\Injector;
use Symfony\Component\Console\Input\InputOption;

final class InjectCommand extends AbstractCommand
{
    public const NAME        = "prototype:inject";
    public const DESCRIPTION = "Inject all prototype dependencies";
    public const OPTIONS     = [
        ['remove', 'r', InputOption::VALUE_OPTIONAL, 'Remove PrototypeTrait', false]
    ];

    /**
     * Perform command.
     *
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function perform(): void
    {
        $targets = $this->getTargets();
        if (empty($targets)) {
            $this->writeln("<comment>No prototyped classes found.</comment>");

            return;
        }

        $injector = new Injector();
        foreach ($targets as $class) {
            $dependencies = $this->fetchDependencies($class);
            foreach ($dependencies as $dependency) {
                if ($dependency instanceof \Throwable) {
                    $this->sprintf(
                        "<fg=red>•</fg=red> %s: <fg=red>%s [f: %s, l: %s]</fg=red>\n",
                        $class->getName(),
                        $dependency->getMessage(),
                        $dependency->getFile(),
                        $dependency->getLine()
                    );

                    continue 2;
                }

                if ($dependency === null) {
                    continue 2;
                }
            }

            $this->sprintf(
                "<fg=green>•</fg=green> %s: injecting %s\n",
                $class->getName(),
                $this->wrapDependencies($dependencies, "<fg=green>%s</fg=green>")
            );

            $classDefinition = $this->fetchDefinition($class, $dependencies);

            try {
                $modified = $injector->injectDependencies(
                    file_get_contents($class->getFileName()),
                    $classDefinition,
                    $this->option('remove')
                );

                file_put_contents($class->getFileName(), $modified);
            } catch (\Throwable $e) {
                $this->sprintf(
                    "<fg=red>•</fg=red> %s: <fg=red>%s [f: %s, l: %s]</fg=red>\n",
                    $class->getName(),
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine()
                );
            }
        }
    }

    /**
     * @param \Spiral\Prototype\Dependency[] $dependencies
     * @param string                         $format
     * @return string
     */
    private function wrapDependencies(array $dependencies, string $format): string
    {
        $output = [];
        foreach ($dependencies as $dependency) {
            $output[] = sprintf($format, "{$dependency->var} ({$dependency->type->fullName})");
        }

        return join(', ', $output);
    }
}