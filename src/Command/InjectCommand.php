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
use Spiral\Prototype\Exception\ClassNotDeclaredException;
use Spiral\Prototype\Injector;
use Symfony\Component\Console\Input\InputOption;

final class InjectCommand extends AbstractCommand
{
    public const NAME        = 'prototype:inject';
    public const DESCRIPTION = 'Inject all prototype dependencies';
    public const OPTIONS     = [
        ['remove', 'r', InputOption::VALUE_NONE, 'Remove PrototypeTrait']
    ];

    /**
     * Perform command.
     *
     * @throws \ReflectionException
     * @throws ClassNotDeclaredException
     */
    public function perform(): void
    {
        $prototyped = $this->locator->getTargetClasses();
        if ($prototyped === []) {
            $this->writeln('<comment>No prototyped classes found.</comment>');

            return;
        }

        foreach ($prototyped as $class) {
            $proto = $this->getPrototypeProperties($class);
            foreach ($proto as $target) {
                print_r(compact('target'));
                if ($target instanceof \Throwable) {
                    $this->sprintf(
                        "<fg=red>•</fg=red> %s: <fg=red>%s [f: %s, l: %s]</fg=red>\n",
                        $class->getName(),
                        $target->getMessage(),
                        $target->getFile(),
                        $target->getLine()
                    );

                    continue 2;
                }

                if ($target === null) {
                    continue 2;
                }
            }

            $this->sprintf(
                "<fg=green>•</fg=green> <fg=yellow>%s</fg=yellow>: inject %s\n",
                $class->getName(),
                $this->mergeTargets($proto, '<fg=cyan>%s</fg=cyan> as <fg=green>%s</fg=green>')
            );

            $classDefinition = $this->extractor->extract($class->getFilename(), $proto);

            try {
                $modified = (new Injector())->injectDependencies(
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
     * @param Dependency[] $dependencies
     * @param string       $format
     * @return string
     */
    private function mergeTargets(array $dependencies, string $format): string
    {
        $output = [];
        foreach ($dependencies as $dependency) {
            $output[] = sprintf($format, $dependency->type->fullName, $dependency->var);
        }

        return join(', ', $output);
    }
}
