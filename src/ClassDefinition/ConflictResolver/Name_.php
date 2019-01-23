<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

use Spiral\Prototype\Utils;

class Name_
{
    /** @var string */
    public $name;

    /** @var int */
    public $sequence = 0;

    public static function createWithSequence(string $name, int $sequence): Name_
    {
        $self = new self();
        $self->name = Utils::trimTrailingDigits($name, $sequence);
        $self->sequence = $sequence;

        return $self;
    }

    public static function create(string $name): Name_
    {
        $self = new self();
        $self->name = $name;

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

    private function __construct()
    {
    }
}