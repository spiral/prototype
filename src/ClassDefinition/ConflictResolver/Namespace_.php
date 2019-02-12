<?php
declare(strict_types=1);

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

class Namespace_ extends AbstractEntity
{
    /** @var string */
    private $fullName;

    public static function createWithSequence(string $name, string $fullName, int $sequence): Namespace_
    {
        $self = new self();
        $self->name = $name;
        $self->sequence = $sequence;
        $self->fullName = $fullName;

        return $self;
    }

    public static function create(string $name, string $fullName): Namespace_
    {
        $self = new self();
        $self->name = $name;
        $self->fullName = $fullName;

        return $self;
    }

    public function equals(Namespace_ $namespace): bool
    {
        return $this->fullName === $namespace->fullName;
    }
}