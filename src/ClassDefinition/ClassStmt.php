<?php

namespace Spiral\Prototype\ClassDefinition;

use Spiral\Prototype\Utils;

class ClassStmt
{
    /** @var string */
    public $name;
    public $shortName;

    /** @var string|null */
    public $alias;

    /** @var bool */
    public $imported;

    public static function createFromImport(string $name, ?string $alias): ClassStmt
    {
        $stmt = self::createFromInstantiation($name, true);
        $stmt->alias = $alias;

        return $stmt;
    }

    public static function createFromInstantiation(string $name, bool $imported): ClassStmt
    {
        $stmt = new self();
        $stmt->name = $name;
        $stmt->shortName = Utils::shortName($name);
        $stmt->imported = $imported;

        return $stmt;
    }

    public function __toString(): string
    {
        return join('.', [$this->name, $this->alias ?? null, $this->imported ? 'true' : 'false']);
    }

    private function __construct()
    {
    }
}