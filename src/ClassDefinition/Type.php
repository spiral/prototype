<?php

namespace Spiral\Prototype\ClassDefinition;

use Spiral\Prototype\Utils;

class Type
{
    /** @var string|null */
    public $shortName;

    /** @var string|null */
    public $alias;

    /** @var string|null */
    public $fullName;

    public static function createWithAlias(string $name, string $alias): Type
    {
        $type = self::create($name);
        $type->alias = $alias;

        return $type;
    }

    public static function create(string $name): Type
    {
        $type = new self();

        $fullName = null;
        if ($type->hasShortName($name)) {
            $fullName = $name;
            $name = Utils::shortName($name);
        }

        $type->shortName = $name;
        $type->fullName = $fullName;

        return $type;
    }

    public static function createEmpty(): Type
    {
        return new self();
    }

    private function hasShortName(string $type): bool
    {
        return mb_strpos($type, '\\') !== false;
    }

    public function getAliasOrShortName(): string
    {
        return $this->alias ?: $this->shortName;
    }

    public function getSlashedShortName(bool $builtIn): string
    {
        $type = $this->shortName;
        if (!$builtIn && !$this->fullName) {
            $type = "\\$type";
        }

        return $type;
    }
}