<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype;

/**
 * Contains aliases and targets for all declared prototype dependencies.
 */
final class PrototypeRegistry
{
    /** @var Dependency[] */
    private $dependencies = [];

    /**
     * PrototypeRegistry constructor.
     */
    public function __construct()
    {
        $this->dependencies = [];
    }

    /**
     * Assign class to prototype property.
     *
     * @param string $property
     * @param string $type
     */
    public function bindProperty(string $property, string $type)
    {
        $this->dependencies[$property] = Dependency::create($property, $type);
    }

    /**
     * @return Dependency[]
     */
    public function getPropertyBindings(): array
    {
        return $this->dependencies;
    }

    /**
     * Resolves the name of prototype dependency into target class name.
     *
     * @param string $name
     * @return Dependency|null
     */
    public function resolveProperty(string $name): ?Dependency
    {
        // @todo: make it cloned?
        return $this->dependencies[$name] ?? null;
    }
}