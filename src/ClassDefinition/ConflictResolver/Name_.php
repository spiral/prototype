<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

class Name_ extends AbstractEntity
{
    public static function createWithSequence(string $name, int $sequence): Name_
    {
        $self = new self();
        $self->name = $name;
        $self->sequence = $sequence;

        return $self;
    }

    public static function create(string $name): Name_
    {
        $self = new self();
        $self->name = $name;

        return $self;
    }
}