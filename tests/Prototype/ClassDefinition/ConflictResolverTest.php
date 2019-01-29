<?php

namespace Spiral\Prototype\Tests\ClassDefinition;

use PHPUnit\Framework\TestCase;
use Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures\TestApp;

class ConflictResolverTest// extends TestCase
{
    const STORE     = ['TestClass.php', 'TestWithConstructorClass.php', 'ChildClass.php'];
    const DIRECTORY = __DIR__ . '/ConflictResolver/Fixtures/';

    /** @var TestApp */
    private $app;

    private $buf = [];

//    public function setUp()
//    {
//        $this->app = TestApp::init([
//            'root'   => __DIR__,
//            'config' => __DIR__,
//            'app'    => __DIR__
//        ], null, false);
//
//        foreach (self::STORE as $name) {
//            $this->buf[$name] = file_get_contents(self::DIRECTORY . $name);
//        }
//    }
//
//    public function tearDown()
//    {
//        foreach ($this->buf as $name => $content) {
//            file_put_contents(self::DIRECTORY . $name, $content);
//        }
//    }

    public function testWithoutConstructor()
    {
        $this->app->bindApp();
    }

    public function testWithConstructor()
    {
    }

    public function testWithParentConstructor()
    {
    }
}