<?php
declare(strict_types=1);

namespace Spiral\Prototype\ClassDefinition;

class ConstructorParam
{
    /** @var string */
    public $name;

    /** @var bool */
    public $nullable;

    /** @var bool */
    public $hasDefault;

    /** @var mixed|null */
    public $default;

    /** @var Type|null */
    public $type;

    /** @var bool */
    private $builtIn;

    public static function createFromReflection(\ReflectionParameter $parameter): ConstructorParam
    {
        $stmt = new self();
        $stmt->name = $parameter->getName();

        if ($parameter->hasType()) {
            $stmt->type = Type::create($parameter->getType()->getName());
            $stmt->builtIn = $parameter->getType()->isBuiltin();
            $stmt->nullable = $parameter->getType()->allowsNull();

            if ($parameter->isDefaultValueAvailable()) {
                $stmt->hasDefault = true;
                $stmt->default = $parameter->getDefaultValue();
            }
        }

        return $stmt;
    }

    public function isBuiltIn(): bool
    {
        return $this->builtIn;
    }

    private function __construct()
    {
    }
}