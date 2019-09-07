<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Bootloader;

use Cycle\ORM;
use Doctrine\Common\Inflector\Inflector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Spiral\Boot\Bootloader;
use Spiral\Bootloader\ConsoleBootloader;
use Spiral\Core\Container;
use Spiral\Prototype\Command;
use Spiral\Prototype\PrototypeRegistry;

/**
 * Manages ide-friendly container injections via PrototypeTrait.
 */
final class PrototypeBootloader implements
    Bootloader\BootloaderInterface,
    Bootloader\DependedInterface,
    Container\SingletonInterface
{
    // Default spiral specific shortcuts, automatically checked on existence.
    private const DEFAULT_SHORTCUTS = [
        'app'          => ['resolve' => 'Spiral\Boot\KernelInterface'],
        'logger'       => 'Psr\Log\LoggerInterface',
        'memory'       => 'Spiral\Boot\MemoryInterface',
        'container'    => 'Psr\Container\ContainerInterface',
        'logs'         => 'Spiral\Logger\LogsInterface',
        'http'         => 'Spiral\Http\Http',
        'console'      => 'Spiral\Console\Console',
        'queue'        => 'Spiral\Jobs\QueueInterface',
        'paginators'   => 'Spiral\Pagination\PaginationProviderInterface',
        'input'        => 'Spiral\Http\Request\InputManager',
        'response'     => 'Spiral\Http\ResponseWrapper',
        'router'       => 'Spiral\Router\RouterInterface',
        'files'        => 'Spiral\Files\FilesInterface',
        'encrypter'    => 'Spiral\Encrypter\EncrypterInterface',
        'classLocator' => 'Spiral\Tokenizer\ClassesInterface',
        'storage'      => 'Spiral\Storage\StorageInterface',
        'views'        => 'Spiral\Views\ViewsInterface',
        'i18n'         => 'Spiral\Translator\TranslatorInterface',
        'dbal'         => 'Spiral\Database\DatabaseProviderInterface',
        'db'           => 'Spiral\Database\DatabaseInterface',
        'orm'          => 'Cycle\ORM\ORMInterface',
        'guard'        => 'Spiral\Security\GuardInterface',
        'validator'    => 'Spiral\Validation\ValidationInterface',
        'snapshots'    => 'Spiral\Snapshots\SnapshotterInterface'
    ];

    /** @var PrototypeRegistry */
    private $registry;

    /**
     * PrototypeBootloader constructor.
     */
    public function __construct()
    {
        $this->registry = new PrototypeRegistry();
    }

    /**
     * @param ConsoleBootloader  $console
     * @param ContainerInterface $container
     */
    public function boot(ConsoleBootloader $console, ContainerInterface $container)
    {
        $console->addCommand(Command\DumpCommand::class);
        $console->addCommand(Command\ListCommand::class);
        $console->addCommand(Command\InjectCommand::class);

        $console->addConfigureSequence(
            'prototype:dump',
            '<fg=magenta>[prototype]</fg=magenta> <fg=cyan>actualizing prototype injections...</fg=cyan>'
        );

        $this->initDefaults($container);
        $this->initCycle($container);
    }

    /**
     * @param string $property
     * @param string $type
     */
    public function bindProperty(string $property, string $type)
    {
        $this->registry->bindProperty($property, $type);
    }

    /**
     * @return array
     */
    public function defineSingletons(): array
    {
        return [PrototypeRegistry::class => $this->registry];
    }

    /**
     * {@inheritdoc}
     */
    public function defineBindings(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function defineDependencies(): array
    {
        return [
            Bootloader\CoreBootloader::class,
            ConsoleBootloader::class,
        ];
    }

    /**
     * @param ContainerInterface $container
     */
    private function initDefaults(ContainerInterface $container)
    {
        foreach (self::DEFAULT_SHORTCUTS as $property => $shortcut) {
            if (is_array($shortcut) && isset($shortcut['resolve'])) {
                try {
                    $target = $container->get($shortcut['resolve']);
                    if (is_object($target)) {
                        $this->bindProperty($property, get_class($target));
                    }

                } catch (ContainerExceptionInterface $e) {
                    continue;
                }
                continue;
            }

            if (
                is_string($shortcut)
                && (class_exists($shortcut, true) || interface_exists($shortcut, true)
                )
            ) {
                $this->bindProperty($property, $shortcut);
            }
        }
    }

    /**
     * @param ContainerInterface $container
     */
    protected function initCycle(ContainerInterface $container)
    {
        if (!$container->has(ORM\SchemaInterface::class)) {
            return;
        }

        /** @var ORM\SchemaInterface $schema */
        $schema = $container->get(ORM\SchemaInterface::class);

        foreach ($schema->getRoles() as $role) {
            $repository = $schema->define($role, ORM\SchemaInterface::REPOSITORY);
            if ($repository === ORM\Select\Repository::class || $repository === null) {
                // default repository can not be wired
                continue;
            }

            $this->bindProperty(Inflector::pluralize($role), $repository);
        }
    }
}