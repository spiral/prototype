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
    public function testNoScopeBinded()
    {
        $t = new TestClass();

        ContainerScope::runScope(new Container(), function () use ($t) {
            $t->getTest();
        });
    }

    public function testOK()
    {
        $t = new TestClass();
        $c = new Container();
        $c->bind('testClass', $t);

        $r = ContainerScope::runScope($c, function () use ($t) {
            return $t->getTest();
        });

        $this->assertSame($t, $r);
    }
}