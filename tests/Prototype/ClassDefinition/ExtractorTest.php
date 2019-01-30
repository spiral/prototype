<?php

namespace Spiral\Prototype\Tests\ClassDefinition;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassDefinition\Extractor;
use Spiral\Prototype\Exception\ClassNotDeclaredException;

class ExtractorTest extends TestCase
{
    /**
     * @throws ClassNotDeclaredException
     */
    public function testNoClass()
    {
        $this->expectException(ClassNotDeclaredException::class);
        $this->getExtractor()->extract(dirname(__DIR__) . '/Fixtures/noClass.php', []);
    }

    private function getExtractor(): Extractor
    {
        $container = new Container();

        return $container->get(Extractor::class);
    }
}