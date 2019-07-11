<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Shortcuts;

class Result
{
    public $defined = false;
    public $bound = false;
    public $boundTo;
    public $resolved = false;

    public static function defined(): self
    {
        $self = new self();
        $self->defined = true;

        return $self;
    }

    public static function bound(string $to): self
    {
        $self = new self();
        $self->bound = true;
        $self->boundTo = $to;

        return $self;
    }

    public static function resolved(): self
    {
        $self = new self();
        $self->resolved = true;

        return $self;
    }

    public static function unresolved(): self
    {
        return new self();
    }
}