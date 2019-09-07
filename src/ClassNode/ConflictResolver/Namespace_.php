<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\ClassNode\ConflictResolver;

final class Namespace_ extends AbstractEntity
{
    /** @var string */
    private $fullName;

    /**
     * @param string $name
     * @param string $fullName
     * @param int    $sequence
     * @return Namespace_
     */
    public static function createWithSequence(string $name, string $fullName, int $sequence): Namespace_
    {
        $self = new self();
        $self->name = $name;
        $self->sequence = $sequence;
        $self->fullName = $fullName;

        return $self;
    }

    /**
     * @param string $name
     * @param string $fullName
     * @return Namespace_
     */
    public static function create(string $name, string $fullName): Namespace_
    {
        $self = new self();
        $self->name = $name;
        $self->fullName = $fullName;

        return $self;
    }

    /**
     * @param Namespace_ $namespace
     * @return bool
     */
    public function equals(Namespace_ $namespace): bool
    {
        return $this->fullName === $namespace->fullName;
    }
}