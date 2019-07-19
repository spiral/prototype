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
use Spiral\Prototype\Shortcuts\Injector;
use Spiral\Prototype\Tests\Commands\Fixtures\Example;
use Spiral\Prototype\Tests\Commands\Fixtures\ResolvedInterface;
use Spiral\Prototype\Tests\Commands\Fixtures\UnresolvedInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class AddShortcutCommandTest extends AbstractCommandsTest
{
    /**
     * @dataProvider failedShortcutsProvider
     *
     * @param string $shortcut
     * @param string $binding
     */
    public function testFailedShortcuts(string $shortcut, string $binding): void
    {
        $this->app->bindApp();
        $out = new BufferedOutput();

        $inp = new ArrayInput(compact('shortcut', 'binding'));
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringContainsString('Input contains next error', $result);
    }

    /**
     *
     * @param string $shortcut
     * @param string $binding
     */
    public function testValidShortcuts(): void
    {
        $this->app->bindApp();
        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringNotContainsString('Input contains next error', $result);
    }

    public function failedShortcutsProvider(): array
    {
        return [
            [
                'shortcut' => '',
                'binding'  => ResolvedInterface::class
            ],
            [
                'shortcut' => 'shortcut',
                'binding'  => ''
            ],
            [
                'shortcut' => '',
                'binding'  => ''
            ]
        ];
    }

    public function testAlreadyAddedShortcut(): void
    {
        $this->app->bindApp();

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);
        $this->clearCache();
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringNotContainsString('already added', $result);

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);

        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringContainsString('already added', $result);
    }

    public function testAlreadyBoundShortcut(): void
    {
        $this->app->bindApp();
        $this->clearCache();

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);
        $this->clearCache();
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringNotContainsString('already bound', $result);

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => Example::class
        ]);

        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringContainsString('already bound', $result);
    }

    public function testSuccessful(): void
    {
        $this->app->bindApp();
        $this->clearCache();

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);
        $this->clearCache();
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringContainsString('success', $result);
    }

    public function testNotResolved(): void
    {
        $this->app->bindApp();
        $this->clearCache();

        $out = new BufferedOutput();
        $inp = new ArrayInput([
            'shortcut' => 'unresolved',
            'binding'  => UnresolvedInterface::class
        ]);

        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, $out);
        $result = $out->fetch();

        $this->assertStringContainsString('not resolved', $result);
    }

    private function clearCache(): void
    {
        $this->injector()->drop();
    }

    private function injector(): Injector
    {
        return $this->app->get(Injector::class);
    }
}