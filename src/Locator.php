<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

use Spiral\Prototyping\Traits\PrototypeTrait;
use Spiral\Tokenizer\ClassesInterface;

class Locator
{
    /** @var ClassesInterface */
    private $classes;

    /**
     * @param ClassesInterface $classes
     */
    public function __construct(ClassesInterface $classes)
    {
        $this->classes = $classes;
    }

    /**
     * Locate all classes requiring de-prototyping.
     *
     * @return array
     */
    public function getTargetClasses(): array
    {
        return $this->classes->getClasses(PrototypeTrait::class);
    }
}