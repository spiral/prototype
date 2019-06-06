<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

abstract class AbstractEntity
{
    /** @var string */
    public $name;

    /** @var int */
    public $sequence = 0;

    /**
     * @return string
     */
    public function fullName(): string
    {
        $name = $this->name;
        if ($this->sequence > 0) {
            $name .= $this->sequence;
        }

        return $name;
    }

    /**
     * AbstractEntity constructor.
     */
    protected function __construct()
    {
    }
}