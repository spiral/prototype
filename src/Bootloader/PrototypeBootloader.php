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
use Spiral\Annotations\AnnotationLocator;
use Spiral\Boot\Bootloader;
use Spiral\Boot\MemoryInterface;
use Spiral\Bootloader\ConsoleBootloader;
use Spiral\Core\Container;
use Spiral\Prototype\Annotation\Prototyped;
use Spiral\Prototype\Command;
use Spiral\Prototype\PrototypeRegistry;

/**
 * Manages ide-friendly container injections via PrototypeTrait.
 */
final class PrototypeBootloader extends Bootloader\Bootloader implements Container\SingletonInterface
{
    // Default spiral specific shortcuts, automatically checked on existence.
    private const DEFAULT_SHORTCUTS = [
        'app'          => ['resolve' => 'Spiral\Boot\KernelInterface'],
        'classLocator' => 'Spiral\Tokenizer\ClassesInterface',
        'console'      => 'Spiral\Console\Console',
        'container'    => 'Psr\Container\ContainerInterface',
        'db'           => 'Spiral\Database\DatabaseInterface',
        'dbal'         => 'Spiral\Database\DatabaseProviderInterface',
        'encrypter'    => 'Spiral\Encrypter\EncrypterInterface',
        'env'          => 'Spiral\Boot\EnvironmentInterface',
        'files'        => 'Spiral\Files\FilesInterface',
        'guard'        => 'Spiral\Security\GuardInterface',
        'http'         => 'Spiral\Http\Http',
        'i18n'         => 'Spiral\Translator\TranslatorInterface',
        'input'        => 'Spiral\Http\Request\InputManager',
        'logger'       => 'Psr\Log\LoggerInterface',
        'logs'         => 'Spiral\Logger\LogsInterface',
        'memory'       => 'Spiral\Boot\MemoryInterface',
        'orm'          => 'Cycle\ORM\ORMInterface',
        'paginators'   => 'Spiral\Pagination\PaginationProviderInterface',
        'queue'        => 'Spiral\Jobs\QueueInterface',
        'request'      => 'Spiral\Http\Request\InputManager',
        'response'     => 'Spiral\Http\ResponseWrapper',
        'router'       => 'Spiral\Router\RouterInterface',
        'server'       => 'Spiral\Goridge\RPC',
        'snapshots'    => 'Spiral\Snapshots\SnapshotterInterface',
        'storage'      => 'Spiral\Storage\StorageInterface',
        'validator'    => 'Spiral\Validation\ValidationInterface',
        'views'        => 'Spiral\Views\ViewsInterface',
    ];

    public const DEPENDENCIES = [Bootloader\CoreBootloader::class, ConsoleBootloader::class,];

    /** @var MemoryInterface */
    private $memory;

    /** @var PrototypeRegistry */
    private $registry;

    /**
     * @param MemoryInterface   $memory
     * @param PrototypeRegistry $registry
     */
    public function __construct(MemoryInterface $memory, PrototypeRegistry $registry)
    {
        $this->memory = $memory;
        $this->registry = $registry;
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

        $console->addUpdateSequence(
            'prototype:dump',
            '<fg=magenta>[prototype]</fg=magenta> <fg=cyan>actualizing prototype injections...</fg=cyan>'
        );

        $this->initDefaults($container);
        $this->initCycle($container);
        $this->initAnnotations($container, false);
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

            if (is_string($shortcut) && (
                    class_exists($shortcut, true) || interface_exists($shortcut, true)
                )
            ) {
                $this->bindProperty($property, $shortcut);
            }
        }
    }

    /**
     * @param ContainerInterface $container
     * @param bool               $reset
     */
    public function initAnnotations(ContainerInterface $container, bool $reset = false)
    {
        $prototyped = $this->memory->loadData('prototyped');
        if (!$reset && $prototyped !== null) {
            foreach ($prototyped as $property => $class) {
                $this->bindProperty($property, $class);
            }

            return;
        }

        /** @var AnnotationLocator $locator */
        $locator = $container->get(AnnotationLocator::class);

        $prototyped = [];
        foreach ($locator->findClasses(Prototyped::class) as $class) {
            $prototyped[$class->getAnnotation()->property] = $class->getClass()->getName();
            $this->bindProperty($class->getAnnotation()->property, $class->getClass()->getName());
        }

        $this->memory->saveData('prototyped', $prototyped);
    }

    /**
     * @param ContainerInterface $container
     */
    public function initCycle(ContainerInterface $container)
    {
        if (!$container->has(ORM\SchemaInterface::class)) {
            return;
        }

        /** @var ORM\SchemaInterface|null $schema */
        $schema = $container->get(ORM\SchemaInterface::class);
        if ($schema === null) {
            return;
        }

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
