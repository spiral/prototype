<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

abstract class AbstractEntity
{
    /** @var string */
    public $name;

    /** @var string */
    public $sequence = 0;

    public function fullName(): string
    {
        $name = $this->name;
        if ($this->sequence > 0) {
            $name .= $this->sequence;
        }

        return $name;
    }

    protected function __construct()
    {
    }
}