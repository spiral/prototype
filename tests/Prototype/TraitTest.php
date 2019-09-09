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
use Spiral\Core\ContainerScope;
use Spiral\Prototype\Exception\PrototypeException;
use Spiral\Prototype\PrototypeRegistry;
use Spiral\Prototype\Tests\Fixtures\TestClass;

class TraitTest extends TestCase
{
    /**
     * @expectedException \Spiral\Core\Exception\ScopeException
     */
    public function testNoScope()
    {
        $t = new TestClass();
        $t->getTest();
    }

    /**
     * @expectedException \Spiral\Core\Exception\ScopeException
     */
    public function testNoScopeBound()
    {
        $t = new TestClass();

        $c = new Container();

        ContainerScope::runScope($c, function () use ($t) {
            $t->getTest();
        });
    }

    /**
     * @expectedException \Spiral\Prototype\Exception\PrototypeException
     */
    public function testCascade()
    {
        $t = new TestClass();
        $c = new Container();
        $c->bindSingleton(PrototypeRegistry::class, $p = new PrototypeRegistry());
        $p->bindProperty('testClass', 'Invalid');

        ContainerScope::runScope($c, function () use ($t) {
            $t->getTest();
        });
    }

    public function testOK()
    {
        $t = new TestClass();
        $c = new Container();
        $c->bindSingleton(PrototypeRegistry::class, $p = new PrototypeRegistry());
        $c->bindSingleton(TestClass::class, $t);
        $p->bindProperty('testClass', TestClass::class);

        $r = ContainerScope::runScope($c, function () use ($t) {
            return $t->getTest();
        });

        $this->assertSame($t, $r);
    }
}
