<?php

namespace Spiral\Prototype\ClassDefinition;

use Spiral\Prototype\Utils;

class ClassStmt
{
    /** @var string */
    public $name;

    /** @var string */
    public $shortName;

    /** @var string|null */
    public $alias;

    public static function create(string $name, ?string $alias): ClassStmt
    {
        $stmt = new self();
        $stmt->name = $name;
        $stmt->shortName = Utils::shortName($name);
        $stmt->alias = $alias;

        return $stmt;
    }

    public function __toString(): string
    {
        if ($this->alias) {
            return "{$this->name} as $this->alias";
        }

        return $this->name;
    }

    private function __construct()
    {
    }
}