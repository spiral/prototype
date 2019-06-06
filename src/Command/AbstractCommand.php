<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Spiral\Console\Command;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Dependency;
use Spiral\Prototype\Extractor;
use Spiral\Prototype\Locator;
use Spiral\Tokenizer\ClassesInterface;

abstract class AbstractCommand extends Command
{
    /** @var ContainerInterface */
    private $container;

    /** @var ClassesInterface */
    private $classes;

    /**
     * @param ContainerInterface $container
     * @param ClassesInterface   $classes
     */
    public function __construct(ContainerInterface $container, ClassesInterface $classes)
    {
        parent::__construct(null);

        $this->container = $container;
        $this->classes = $classes;
    }

    /**
     * @return array|\ReflectionClass[]
     */
    public function getTargets(): array
    {
        $locator = new Locator($this->classes);

        return $locator->getTargetClasses();
    }

    /**
     * @param \ReflectionClass $class
     * @param array            $dependencies
     * @return ClassDefinition
     *
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    protected function fetchDefinition(\ReflectionClass $class, array $dependencies): ClassDefinition
    {
        /** @var ClassDefinition\Extractor $extractor */
        $extractor = $this->container->get(ClassDefinition\Extractor::class);

        return $extractor->extract($class->getFilename(), $dependencies);
    }

    /**
     * Fetch class dependencies.
     *
     * @param \ReflectionClass $class
     * @return array
     */
    protected function fetchDependencies(\ReflectionClass $class): array
    {
        $extractor = $this->container->get(Extractor::class);
        $dependencies = $extractor->getPrototypeNames(file_get_contents($class->getFilename()));

        return $this->resolveDependencies($dependencies);
    }

    /**
     * Return list of class dependencies with value equal to external class or
     * an instance of container exception.
     *
     * @param array $deps
     * @return array|Dependency[]
     */
    private function resolveDependencies(array $deps): array
    {
        $result = [];

        foreach ($deps as $name) {
            if (!$this->container->has($name)) {
                $result[$name] = null;
                continue;
            }

            try {
                $result[$name] = Dependency::create(get_class($this->container->get($name)), $name);
            } catch (ContainerExceptionInterface $e) {
                $result[$name] = $e;
            }
        }

        return $result;
    }
}