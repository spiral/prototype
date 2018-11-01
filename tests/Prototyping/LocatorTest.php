<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototyping\Locator;
use Spiral\Prototyping\Tests\Fixtures\HydratedClass;
use Spiral\Prototyping\Tests\Fixtures\TestClass;
use Spiral\Tokenizer\ClassesInterface;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;
use Spiral\Tokenizer\TokenizerInterface;

class LocatorTest extends TestCase
{
    public function testLocate()
    {
        $classes = $this->makeClasses();
        $l = new Locator($classes);

        $this->assertArrayHasKey(TestClass::class, $l->getTargetClasses());
    }

    public function testLocateNot()
    {
        $classes = $this->makeClasses();
        $l = new Locator($classes);

        $this->assertArrayNotHasKey(HydratedClass::class, $l->getTargetClasses());
    }

    private function makeClasses(): ClassesInterface
    {
        $c = new Container();
        $c->bind(TokenizerConfig::class, new TokenizerConfig([
            'directories' => [__DIR__ . '/Fixtures'],
            'exclude'     => []
        ]));

        $c->bind(TokenizerInterface::class, Tokenizer::class);
        $c->bind(ClassesInterface::class, ClassLocator::class);

        return $c->get(ClassesInterface::class);
    }
}
