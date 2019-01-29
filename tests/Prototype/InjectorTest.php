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

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
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

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
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

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
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

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
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

    /**
     * @param string $filename
     * @param array  $dependencies
     *
     * @return ClassDefinition
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    private function getDefinition(string $filename, array $dependencies): ClassDefinition
    {
        return $this->getExtractor()->extract($filename, Dependencies::convert($dependencies));
    }

    private function getExtractor(): ClassDefinition\Extractor
    {
        $container = new Container();

        return $container->get(ClassDefinition\Extractor::class);
    }
}
