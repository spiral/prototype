<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Traits;

use Spiral\Core\ContainerScope;
use Spiral\Core\Exception\ScopeException;
use Spiral\Prototype\Exception\PrototypeException;
use Spiral\Prototype\PrototypeRegistry;

/**
 * This DocComment is auto-generated, do not edit or commit this file to repository.
 * 
 * @property \App\App $app
 * @property \Psr\Log\LoggerInterface $logger
 * @property \Spiral\Boot\MemoryInterface $memory
 * @property \Psr\Container\ContainerInterface $container
 * @property \Spiral\Logger\LogsInterface $logs
 * @property \Spiral\Http\Http $http
 * @property \Spiral\Console\Console $console
 * @property \Spiral\Jobs\QueueInterface $queue
 * @property \Spiral\Pagination\PaginationProviderInterface $paginators
 * @property \Spiral\Http\Request\InputManager $request
 * @property \Spiral\Http\Request\InputManager $input
 * @property \Spiral\Http\ResponseWrapper $response
 * @property \Spiral\Router\RouterInterface $router
 * @property \Spiral\Files\FilesInterface $files
 * @property \Spiral\Encrypter\EncrypterInterface $encrypter
 * @property \Spiral\Tokenizer\ClassesInterface $classLocator
 * @property \Spiral\Views\ViewsInterface $views
 * @property \Spiral\Translator\TranslatorInterface $i18n
 * @property \Spiral\Database\DatabaseProviderInterface $dbal
 * @property \Spiral\Database\DatabaseInterface $db
 * @property \Cycle\ORM\ORMInterface $orm
 * @property \Spiral\Security\GuardInterface $guard
 * @property \Spiral\Validation\ValidationInterface $validator
 * @property \Spiral\Snapshots\SnapshotterInterface $snapshots
 * @property \App\UserRepository $users
 */
trait PrototypeTrait
{
    /**
     * Automatic resolution of scoped dependency to it's value. Relies
     * on global container scope.
     *
     * @param string $name
     * @return mixed
     *
     * @throws ScopeException
     */
    public function __get(string $name)
    {
        $container = ContainerScope::getContainer();
        if ($container === null || !$container->has(PrototypeRegistry::class)) {
            throw new ScopeException(
                "Unable to resolve prototyped dependency `{$name}`, invalid container scope"
            );
        }

        /** @var PrototypeRegistry $registry */
        $registry = $container->get(PrototypeRegistry::class);

        $target = $registry->resolveDependency($name);
        if ($target === null) {
            throw new PrototypeException("Undefined prototype property `{$name}`");
        }

        return $container->get($target->type->fullName);
    }
}