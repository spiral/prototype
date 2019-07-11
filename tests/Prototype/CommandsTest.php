<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Console\Console;
use Spiral\Core\Container;
use Spiral\Prototype\Tests\Fixtures\TestApp;
use Spiral\Prototype\Tests\Fixtures\TestClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    private const STORE = ['TestClass.php', 'ChildClass.php', 'ChildWithConstructorClass.php', 'WithConstructor.php'];

    /** @var TestApp */
    private $app;

    private $buf = [];

    public function setUp()
    {
        $this->app = TestApp::init([
            'root'   => __DIR__,
            'config' => __DIR__,
            'app'    => __DIR__
        ], null, false);

        foreach (self::STORE as $name) {
            $this->buf[$name] = file_get_contents(__DIR__ . '/Fixtures/' . $name);
        }
    }

    public function tearDown()
    {
        foreach (self::STORE as $name) {
            file_put_contents(__DIR__ . '/Fixtures/' . $name, $this->buf[$name]);
        }
    }

    public function testList(): void
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('prototype:list', $result);
        $this->assertContains('prototype:inject', $result);
    }

    public function testListPrototypes(): void
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertContains('undefined', $result);
    }

    public function testListPrototypesBinded(): void
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertNotContains('undefined', $result);
        $this->assertNotContains('Undefined class', $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testListPrototypesBindedWithoutResolve(): void
    {
        $this->app->bindWithoutResolver();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertContains('Undefined class', $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testInject(): void
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertContains(TestClass::class, $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testInjectNone(): void
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertSame("", $result);
    }

    public function testInjectInvalid(): void
    {
        $this->app->get(Container::class)->bind('testClass', 'Invalid');

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertContains("Undefined class", $result);
        $this->assertContains("Invalid", $result);
    }
}