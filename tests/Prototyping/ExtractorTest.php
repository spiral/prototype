<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Prototyping\Extractor;

class ExtractorTest extends TestCase
{
    public function testExtract()
    {
        $e = new Extractor();
        $this->assertSame(['testClass'], $e->getPrototypedDependencies(__DIR__ . '/Fixtures/TestClass.php'));
    }

    public function testExtractNone()
    {
        $e = new Extractor();
        $this->assertSame([], $e->getPrototypedDependencies(__DIR__ . '/Fixtures/HydratedClass.php'));
    }
}
