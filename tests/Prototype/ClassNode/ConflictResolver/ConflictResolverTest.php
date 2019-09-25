<?php

namespace Spiral\Prototype\Tests\ClassNode\ConflictResolver;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassNode;
use Spiral\Prototype\Injector;
use Spiral\Prototype\NodeExtractor;
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures;
use Spiral\Prototype\Tests\Fixtures\Dependencies;

class ConflictResolverTest extends TestCase
{
    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testResolveInternalConflicts()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/TestClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, [
                'test'  => Fixtures\Test::class,
                'test2' => Fixtures\SubFolder\Test::class,
                'test3' => Fixtures\ATest3::class,
            ])
        );

        $this->assertContains(Fixtures\Test::class . ';', $r);
        $this->assertRegExp('/@var Test[\s|\r\n]/', $r);
        $this->assertContains('@param Test $test', $r);

        $this->assertContains(Fixtures\SubFolder\Test::class . ' as Test2;', $r);
        $this->assertNotContains(Fixtures\SubFolder\Test::class . ';', $r);
        $this->assertRegExp('/@var Test2[\s|\r\n]/', $r);
        $this->assertContains('@param Test2 $test2', $r);

        $this->assertContains(Fixtures\ATest3::class . ';', $r);
        $this->assertRegExp('/@var ATest3[\s|\r\n]/', $r);
        $this->assertContains('@param ATest3 $test3', $r);
    }

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testResolveImportConflicts(): void
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/TestClassWithImports.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, [
                'test'  => Fixtures\Test::class,
                'test2' => Fixtures\SubFolder\Test::class,
                'test3' => Fixtures\ATest3::class,
            ])
        );

        $this->assertContains(Fixtures\Test::class . ' as FTest;', $r);
        $this->assertNotContains(Fixtures\Test::class . ';', $r);
        $this->assertRegExp('/@var FTest[\s|\r\n]/', $r);
        $this->assertContains('@param FTest $test', $r);

        $this->assertContains(Fixtures\SubFolder\Test::class . ' as TestAlias;', $r);
        $this->assertNotContains(Fixtures\SubFolder\Test::class . ';', $r);
        $this->assertRegExp('/@var TestAlias[\s|\r\n]/', $r);
        $this->assertContains('@param TestAlias $test2', $r);

        $this->assertContains(Fixtures\ATest3::class . ' as ATest;', $r);
        $this->assertNotContains(Fixtures\ATest3::class . ';', $r);
        $this->assertRegExp('/@var ATest[\s|\r\n]/', $r);
        $this->assertContains('@param ATest $test3', $r);
    }

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testResolveWithAliasForParentConstructor()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/ChildClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, [
                'test'  => Fixtures\Test::class,
                'test2' => Fixtures\SubFolder\Test::class,
                'test3' => Fixtures\ATest3::class,
            ])
        );

        $this->assertContains(Fixtures\Test::class . ';', $r);
        $this->assertRegExp('/@var Test[\s|\r\n]/', $r);
        $this->assertContains('@param Test $test', $r);

        $this->assertContains(Fixtures\SubFolder\Test::class . ' as Test2;', $r);
        $this->assertNotContains(Fixtures\SubFolder\Test::class . ';', $r);
        $this->assertRegExp('/@var Test2[\s|\r\n]/', $r);
        $this->assertContains('@param Test2 $test2', $r);

        $this->assertContains(Fixtures\ATest3::class . ' as ATestAlias;', $r);
        $this->assertNotContains(Fixtures\ATest3::class . ';', $r);
        $this->assertRegExp('/@var ATestAlias[\s|\r\n]/', $r);
        $this->assertContains('@param ATestAlias $test3', $r);
    }

    /**
     * @param string $filename
     * @param array  $dependencies
     *
     * @return ClassNode
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    private function getDefinition(string $filename, array $dependencies): ClassNode
    {
        return $this->getExtractor()->extract($filename, Dependencies::convert($dependencies));
    }

    private function getExtractor(): NodeExtractor
    {
        $container = new Container();

        return $container->get(NodeExtractor::class);
    }
}
