<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

use Spiral\Prototype\Utils;

class Namespace_
{
    /** @var string */
    public $name;

    /** @var int */
    public $sequence = 0;

    /** @var string */
    private $fullName;

    public static function createWithSequence(string $shortName, string $fullName, int $sequence): Namespace_
    {
        $self = new self();
        $self->name = Utils::trimTrailingDigits($shortName, $sequence);
        $self->sequence = $sequence;
        $self->fullName = $fullName;

        return $self;
    }

    public static function create(string $shortName, string $fullName): Namespace_
    {
        $self = new self();
        $self->name = $shortName;
        $self->fullName = $fullName;

        return $self;
    }

    public function fullName(): string
    {
        $name = $this->name;
        if ($this->sequence > 0) {
            $name .= $this->sequence;
        }

        return $name;
    }

    public function equals(Namespace_ $namespace): bool
    {
        return $this->fullName === $namespace->fullName;
    }

    private function __construct()
    {
    }
}