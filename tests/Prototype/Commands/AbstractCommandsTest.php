<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Spiral\Prototype\Tests\Fixtures\TestApp;

abstract class AbstractCommandsTest extends TestCase
{
    protected const STORE = ['TestClass.php', 'ChildClass.php', 'ChildWithConstructorClass.php', 'WithConstructor.php'];

    /** @var TestApp */
    protected $app;

    protected $buf = [];

    public function setUp()
    {
        $this->app = TestApp::init([
            'root'   => $this->dir(),
            'config' => $this->dir(),
            'app'    => $this->dir(),
            'cache'  => sys_get_temp_dir()
        ], null, false);

        foreach (self::STORE as $name) {
            $this->buf[$name] = file_get_contents($this->dir() . '/Fixtures/' . $name);
        }
    }

    public function tearDown()
    {
        foreach (self::STORE as $name) {
            file_put_contents($this->dir() . '/Fixtures/' . $name, $this->buf[$name]);
        }
    }

    private function dir(): string
    {
        return dirname(__DIR__);
    }
}
