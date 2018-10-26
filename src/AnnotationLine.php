<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

/**
 * Singular annotation line.
 */
final class AnnotationLine
{
    /** @var string */
    public $value = '';

    /** @var string|null */
    public $type = null;

    /**
     * @param string      $value
     * @param string|null $type
     */
    public function __construct(string $value, string $type = null)
    {
        $this->value = $value;
        $this->type = $type;
    }
}