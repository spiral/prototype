<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype;

use Spiral\Prototype\ClassDefinition\Type;

final class Dependency
{
    /** @var Type */
    public $type;

    /** @var string */
    public $property;

    /** @var string */
    public $var;

    /**
     * @param string $type
     * @param string $name
     * @return Dependency
     */
    public static function create(string $type, string $name): Dependency
    {
        $dependency = new self();
        $dependency->type = Type::create($type);
        $dependency->property = $name;
        $dependency->var = $name;

        return $dependency;
    }

    /**
     * Dependency constructor.
     */
    private function __construct()
    {
    }
}