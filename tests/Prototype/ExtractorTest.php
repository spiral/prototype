<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Prototype\Extractor;

class ExtractorTest extends TestCase
{
    public function testExtract()
    {
        $e = new Extractor();
        $this->assertSame(
            ['testClass'],
            $e->getPrototypeNames(file_get_contents(__DIR__ . '/Fixtures/TestClass.php'))
        );
    }

    public function testExtractNone()
    {
        $e = new Extractor();
        $this->assertSame(
            [],
            $e->getPrototypeNames(file_get_contents(__DIR__ . '/Fixtures/HydratedClass.php'))
        );
    }
}
