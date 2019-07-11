<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Bootloader;

use Spiral\Boot\Bootloader;
use Spiral\Boot\MemoryInterface;
use Spiral\Bootloader\ConsoleBootloader;
use Spiral\Prototype\Command;

final class PrototypeBootloader extends Bootloader\Bootloader implements Bootloader\DependedInterface
{
    public const MEMORY_SECTION = 'prototype:shortcuts';

    private const SHORTCUTS = [
    ];

    /** @var MemoryInterface */
    private $memory;

    public function __construct(MemoryInterface $memory)
    {
        $this->memory = $memory;
    }

    /**
     * @param ConsoleBootloader $console
     */
    public function boot(ConsoleBootloader $console)
    {
        $console->addCommand(Command\ListCommand::class);
        $console->addCommand(Command\InjectCommand::class);
        $console->addCommand(Command\AddShortcutCommand::class);
    }

    /**
     * {@inheritdoc}
     */
    public function defineBindings(): array
    {
        return array_merge($this->memory->loadData(self::MEMORY_SECTION), static::SHORTCUTS, static::BINDINGS);
    }

    /**
     * @return array
     */
    public function defineDependencies(): array
    {
        return [
            ConsoleBootloader::class,
            Bootloader\CoreBootloader::class
        ];
    }
}