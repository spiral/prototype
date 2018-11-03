<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Console\ConsoleCore;
use Spiral\Prototype\Tests\Fixtures\TestApp;
use Spiral\Prototype\Tests\Fixtures\TestClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    const STORE = ['TestClass.php', 'WithConstructor.php'];

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

    public function testList()
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(ConsoleCore::class)->run('list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('prototype:list', $result);
        $this->assertContains('prototype:inject', $result);
    }

    public function testListPrototypes()
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(ConsoleCore::class)->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertContains('undefined', $result);
    }

    public function testListPrototypesBinded()
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(ConsoleCore::class)->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertNotContains('undefined', $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testInject()
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(ConsoleCore::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertContains(TestClass::class, $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testInjectNone()
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(ConsoleCore::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertSame("", $result);
    }
}