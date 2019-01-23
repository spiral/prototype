<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Dependency;
use Spiral\Prototype\Injector;
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
            file_get_contents(__DIR__ . '/Fixtures/TestClass.php'),
            $this->getDefinition($filename, ['testClass' => TestClass::class])
        );

        $this->assertContains(TestClass::class, $r);
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
        $extractor = new ClassDefinition\Extractor();

        return $extractor->extract(file_get_contents($filename), $this->convertDependencies($dependencies));
    }

    private function convertDependencies(array $deps): array
    {
        $converted = [];
        foreach ($deps as $name => $type) {
            $converted[$name] = Dependency::create($type, $name);
        }

        return $converted;
    }
}
