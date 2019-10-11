<?php

declare(strict_types=1);

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Prototype\Traits\PrototypeTrait;

class TestEmptyClass
{
    use PrototypeTrait;

    public function getTest(): void
    {
    }

    public function method(): void
    {
    }
}
