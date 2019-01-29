<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Injector;
use Spiral\Prototype\Tests\Fixtures\Dependencies;
use Spiral\Prototype\Tests\Fixtures\TestClass;

class InjectorTest extends TestCase
{
    public function setUp()
    {
        if (ini_get('zend.assertions') == 1) {
            ini_set('zend.assertions', 0);
        }
    }

    public function testSimpleInjection()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/TestClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains(TestClass::class, $r);
    }

    public function testParentConstructorCallInjection()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/ChildClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains(TestClass::class, $r);
        $this->assertContains('parent::__construct(', $r);
    }

    public function testNoParentConstructorCallInjection()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/ChildWithConstructorClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains(TestClass::class, $r);
        $this->assertNotContains('parent::__construct(', $r);
    }

    public function testModifyConstructor()
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/WithConstructor.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains('@param HydratedClass $h', $r);
        $this->assertContains('@param TestClass $testClass', $r);
    }

    private function getDefinition(string $filename, array $dependencies): ClassDefinition
    {
        $container = new Container();
        $extractor = $container->get(ClassDefinition\Extractor::class);

        return $extractor->extract(file_get_contents($filename), Dependencies::convert($dependencies));
    }
}
