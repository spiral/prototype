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
use Spiral\Prototype\ClassNode;
use Spiral\Prototype\Injector;
use Spiral\Prototype\NodeExtractor;
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures as ResolverFixtures;
use Spiral\Prototype\Tests\Fixtures\Dependencies;
use Spiral\Prototype\Tests\Fixtures\TestClass;

class InjectorTest extends TestCase
{
    public function setUp(): void
    {
        if ((string)ini_get('zend.assertions') === 1) {
            ini_set('zend.assertions', 0);
        }
    }

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testSimpleInjection(): void
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
    public function testTraitRemove(): void
    {
        $i = new Injector();

        $filename = __DIR__ . '/Fixtures/TestClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains('use PrototypeTrait;', $r);

        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, ['testClass' => TestClass::class]),
            true
        );

        $this->assertNotContains('use PrototypeTrait;', $r);
    }

    /**
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testParentConstructorCallInjection(): void
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
    public function testNoParentConstructorCallInjection(): void
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
    public function testModifyConstructor(): void
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
     * @throws \ReflectionException
     * @throws \Spiral\Prototype\Exception\ClassNotDeclaredException
     */
    public function testParentConstructorParamsTypeDefinition(): void
    {
        $i = new Injector();

        $filename = __DIR__ . '/ClassNode/ConflictResolver/Fixtures/ChildClass.php';
        $r = $i->injectDependencies(
            file_get_contents($filename),
            $this->getDefinition($filename, [
                'test'  => ResolverFixtures\Test::class,
                'test2' => ResolverFixtures\SubFolder\Test::class,
                'test3' => ResolverFixtures\ATest3::class,
            ])
        );

        $this->assertContains('string $str1,', $r);
        $this->assertContains('* @param string $str', $r);

        $this->assertContains(', $var,', $r); //adding ", " to show that there's no type
        $this->assertContains(' * @param $var', $r);

        //Parameter type ATest3 has an alias in a child class
        $this->assertContains('ATestAlias $testApp,', $r);
        $this->assertNotContains('ATest3 $testApp,', $r);
        $this->assertContains('@param ATestAlias $testApp', $r);
        $this->assertNotContains('@param ATest3 $testApp', $r);

        $this->assertContains('?string $str2,', $r);
        $this->assertContains('* @param string|null $str2', $r);

        $this->assertContains('?\StdClass $nullableClass1,', $r);
        $this->assertContains('* @param \StdClass|null $nullableClass1', $r);

        $this->assertContains('?Test $test1 = null,', $r);
        $this->assertContains('* @param Test|null $test1', $r);

        $this->assertContains('?string $str3 = null,', $r);
        $this->assertContains('* @param string|null $str3', $r);

        $this->assertContains('?int $int = 123,', $r);
        $this->assertContains('* @param int|null $int', $r);

        $this->assertContains('?\StdClass $nullableClass2 = null,', $r);
        $this->assertContains('* @param \StdClass|null $nullableClass2', $r);
    }

    /**
     * @param string $filename
     * @param array  $dependencies
     *
     * @return ClassNode
     * @throws \ReflectionException
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
