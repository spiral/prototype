<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Shortcuts;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Boot\MemoryInterface;
use Spiral\Core\Container;
use Spiral\Prototype\Bootloader\PrototypeBootloader;

/**
 * Injects needed shortcuts into a bootloader
 */
final class Injector
{
    /** @var \Spiral\Boot\MemoryInterface */
    private $memory;

    private const SECTION = PrototypeBootloader::MEMORY_SECTION;

    /** @var Container */
    private $container;

    public function __construct(MemoryInterface $memory, Container $container)
    {
        $this->memory = $memory;
        $this->container = $container;
    }

    public function inject(string $shortcut, string $binding): Result
    {
        $shortcuts = $this->getShortcuts();
        if ($this->shortcutAlreadyDefined($shortcuts, $shortcut, $binding)) {
            return Result::defined();
        }

        if ($this->shortcutAlreadyBound($shortcuts, $shortcut, $binding)) {
            return Result::bound($this->boundTo($shortcuts, $shortcut));
        }

        if (!$this->isResolved($binding)) {
            return Result::unresolved();
        }

        $this->memory->saveData(self::SECTION, array_merge($shortcuts, [$shortcut => $binding]));
        $this->container->bind($shortcut, $binding);

        return Result::resolved();
    }

    public function drop(): array
    {
        $shortcuts = $this->getShortcuts();
        $this->memory->saveData(self::SECTION, []);

        foreach (array_keys($shortcuts) as $shortcut) {
            $this->container->removeBinding($shortcut);
        }

        return $shortcuts;
    }

    private function getShortcuts(): array
    {
        $shortcuts = $this->memory->loadData(self::SECTION);
        if (empty($shortcuts) || !is_array($shortcuts)) {
            return [];
        }

        return $shortcuts;
    }

    private function shortcutAlreadyDefined(array $shortcuts, string $shortcut, string $binding): bool
    {
        return isset($shortcuts[$shortcut]) && strcasecmp($shortcuts[$shortcut], $binding) === 0;
    }

    private function shortcutAlreadyBound($shortcuts, string $shortcut, string $binding): bool
    {
        if (isset($shortcuts[$shortcut])) {
            return strcasecmp($shortcuts[$shortcut], $binding) !== 0;
        }

        return isset($this->container->getBindings()[$shortcut]);
    }

    private function boundTo($shortcuts, string $shortcut): string
    {
        if (isset($shortcuts[$shortcut])) {
            return $shortcuts[$shortcut];
        }

        $bound = $this->container->getBindings()[$shortcut];

        if (is_object($bound)) {
            return get_class($bound);
        }

        if (is_scalar($bound)) {
            return (string)$bound;
        }

        return json_encode($bound);
    }

    private function isResolved(string $binding): bool
    {
        try {
            $this->container->get($binding);

            return true;
        } catch (ContainerExceptionInterface $e) {
        }

        try {
            $reflection = new \ReflectionClass($binding);

            return !$reflection->isAbstract() && !$reflection->isTrait() && !$reflection->isInterface();
        } catch (\Throwable $e) {
        }

        return false;
    }
}