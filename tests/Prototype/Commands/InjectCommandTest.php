<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Tests\Commands;

use Spiral\Console\Console;
use Spiral\Prototype\PrototypeRegistry;
use Spiral\Prototype\Tests\Fixtures\TestApp;
use Spiral\Prototype\Tests\Fixtures\TestClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class InjectCommandTest extends AbstractCommandsTest
{
    public function testValid(): void
    {
        $this->app->bindApp();

        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertContains(TestClass::class, $result);
        $this->assertContains(TestApp::class, $result);
    }

    public function testNone(): void
    {
        $inp = new ArrayInput([]);
        $out = new BufferedOutput();
        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);

        $result = $out->fetch();

        $this->assertSame('', $result);
    }

    // todo: fix this test
//    public function testInvalid(): void
//    {
//        $this->app->get(PrototypeRegistry::class)->bindProperty('testClass', 'Invalid');
//
//        $inp = new ArrayInput([]);
//        $out = new BufferedOutput();
//        $this->app->get(Console::class)->run('prototype:inject', $inp, $out);
//
//        $result = $out->fetch();
//
//        $this->assertContains('Undefined class', $result);
//        $this->assertContains('Invalid', $result);
//    }
}