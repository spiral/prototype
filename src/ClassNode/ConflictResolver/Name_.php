<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\ClassNode\ConflictResolver;

final class Name_ extends AbstractEntity
{
    /**
     * @param string $name
     * @param int    $sequence
     * @return Name_
     */
    public static function createWithSequence(string $name, int $sequence): Name_
    {
        $self = new self();
        $self->name = $name;
        $self->sequence = $sequence;

        return $self;
    }

    /**
     * @param string $name
     * @return Name_
     */
    public static function create(string $name): Name_
    {
        $self = new self();
        $self->name = $name;

        return $self;
    }
}
