<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Prototype\ClassNode;

final class ConstructorParam
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

    /**
     * ConstructorParam constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return ConstructorParam
     *
     * @throws \ReflectionException
     */
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

    /**
     * @return bool
     */
    public function isBuiltIn(): bool
    {
        return $this->builtIn;
    }
}
