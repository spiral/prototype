<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests;

use PHPUnit\Framework\TestCase;
use Spiral\Bootloader\Dispatcher\ConsoleBootloader;
use Spiral\Console\ConsoleCore;
use Spiral\Core\Kernel;
use Spiral\Prototype\Bootloader\PrototypeBootloader;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class CommandsTest extends TestCase
{
    /** @var TestApp */
    private $app;

    public function setUp()
    {
        $this->app = TestApp::init([
            'root'   => __DIR__,
            'config' => __DIR__,
            'app'    => __DIR__
        ], null, false);
    }

    public function testList()
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->getConsole()->run('list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('prototype:list', $result);
        $this->assertContains('prototype:inject', $result);
    }

    public function testListPrototypes()
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->getConsole()->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertContains('undefined', $result);
    }

    public function testListPrototypesBinded()
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->getConsole()->run('prototype:list', $inp, $out);

        $result = $out->fetch();

        $this->assertContains('testClass', $result);
        $this->assertNotContains('undefined', $result);
        $this->assertContains(TestApp::class, $result);
    }
}

class TestApp extends Kernel
{
    const LOAD = [
        ConsoleBootloader::class,
        PrototypeBootloader::class
    ];

    public function bindApp()
    {
        $this->container->bind('testClass', self::class);
    }

    /**
     * @return ConsoleCore
     */
    public function getConsole(): ConsoleCore
    {
        return $this->container->get(ConsoleCore::class);
    }
}