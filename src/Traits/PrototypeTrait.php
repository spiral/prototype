<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Traits;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\ContainerScope;
use Spiral\Core\Exception\ScopeException;

trait PrototypeTrait
{
    /**
     * Automatic resolution of scoped dependecy to it's value. Relies
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
        if (empty($container) || !$container->has($name)) {
            throw new ScopeException(
                "Unable to get prototyped dependency `{$name}`, invalid container scope"
            );
        }

        try {
            return $container->get($name);
        } catch (ContainerExceptionInterface $e) {
            throw new ScopeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}