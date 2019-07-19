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
use Spiral\Console\Command;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Dependency;
use Spiral\Prototype\Extractor;
use Spiral\Prototype\Locator;
use Spiral\Tokenizer\ClassesInterface;

abstract class AbstractCommand extends Command
{
    /** @var ClassesInterface */
    private $classes;

    /**
     * @param ClassesInterface $classes
     */
    public function __construct(ClassesInterface $classes)
    {
        parent::__construct(null);
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
     * @throws \ReflectionException
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
        /** @var Extractor $extractor */
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
            $result[$name] = $this->resolveDependency($name);
        }

        return $result;
    }

    /**
     * @param string $name
     * @return \Exception|ContainerExceptionInterface|null|string
     */
    private function resolveDependency(string $name)
    {
        if (!$this->container->has($name)) {
            return null;
        }

        if (!isset($this->container->getBindings()[$name])) {
            return null;
        }

        $binding = $this->container->getBindings()[$name];

        try {
            $this->container->get($name);

            return Dependency::create($binding, $name);
        } catch (ContainerExceptionInterface $e) {
            return $e;
        }
    }
}