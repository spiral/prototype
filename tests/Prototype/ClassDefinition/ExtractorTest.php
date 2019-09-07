<?php

namespace Spiral\Prototype\Tests\ClassDefinition;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassNode\DefinitionExtractor;
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

    private function getExtractor(): DefinitionExtractor
    {
        $container = new Container();

        return $container->get(DefinitionExtractor::class);
    }
}