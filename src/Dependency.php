<?php

namespace Spiral\Prototype;

use Spiral\Prototype\ClassDefinition\Type;

class Dependency
{
    /** @var Type */
    public $type;

    /** @var string */
    public $property;

    /** @var string */
    public $var;

    public static function create(string $type, string $name): Dependency
    {
        $dependency = new self();
        $dependency->type = Type::create($type);
        $dependency->property = $name;
        $dependency->var = $name;

        return $dependency;
    }

    private function __construct()
    {
    }
}