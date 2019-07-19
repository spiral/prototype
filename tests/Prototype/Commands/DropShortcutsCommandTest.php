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
use Spiral\Core\Exception\Container\ContainerException;
use Spiral\Prototype\Tests\Commands\Fixtures\ResolvedInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DropShortcutsCommandTest extends AbstractCommandsTest
{
    public function testDrop(): void
    {
        $this->app->bindApp();
        $inp = new ArrayInput([
            'shortcut' => 'shortcut',
            'binding'  => ResolvedInterface::class
        ]);
        $this->app->get(Console::class)->run('prototype:addShortcut', $inp, new BufferedOutput());

        $this->assertNotNull($this->get('shortcut'));

        $this->app->get(Console::class)->run('prototype:dropShortcuts', new ArrayInput([]), new BufferedOutput());
        $this->assertNull($this->get('shortcut'));
    }

    private function get(string $shortcut)
    {
        try {
            return $this->app->get($shortcut);
        } catch (ContainerException $exception) {
            return null;
        }
    }
}