<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\ClassNode\ConflictResolver;

use Spiral\Prototype\ClassNode;
use Spiral\Prototype\Utils;

final class Namespaces
{
    /** @var Sequences */
    private $sequences;

    /**
     * @param Sequences $sequences
     */
    public function __construct(Sequences $sequences)
    {
        $this->sequences = $sequences;
    }

    /**
     * @param ClassNode $definition
     */
    public function resolve(ClassNode $definition): void
    {
        $namespaces = $this->getReservedNamespaces($definition);
        $counters = $this->initiateCounters($namespaces);

        $this->resolveImportsNamespaces($definition, $counters);
    }

    /**
     * @param ClassNode $definition
     * @return array
     */
    private function getReservedNamespaces(ClassNode $definition): array
    {
        $namespaces = [];
        $namespaces = $this->getReservedNamespacesWithAlias($definition, $namespaces);
        $namespaces = $this->getReservedNamespacesWithoutAlias($definition, $namespaces);

        return $namespaces;
    }

    /**
     * @param ClassNode $definition
     * @param array     $namespaces
     * @return array
     */
    private function getReservedNamespacesWithAlias(ClassNode $definition, array $namespaces): array
    {
        foreach ($definition->getStmts() as $stmt) {
            if (!$stmt->alias) {
                continue;
            }

            $namespaces[$stmt->alias] = $stmt->name;
        }

        return $namespaces;
    }

    /**
     * @param ClassNode $definition
     * @param array     $namespaces
     * @return array
     */
    private function getReservedNamespacesWithoutAlias(ClassNode $definition, array $namespaces): array
    {
        foreach ($definition->getStmts() as $stmt) {
            if ($stmt->alias || isset($namespaces[$stmt->shortName])) {
                continue;
            }

            $namespaces[$stmt->shortName] = $stmt->name;
        }

        return $namespaces;
    }

    /**
     * @param array $namespaces
     * @return array
     */
    private function initiateCounters(array $namespaces): array
    {
        $counters = [];
        foreach ($namespaces as $shortName => $fullName) {
            $namespace = $this->parseNamespace($shortName, $fullName);

            if (isset($counters[$namespace->name])) {
                $counters[$namespace->name][$namespace->sequence] = $namespace;
            } else {
                $counters[$namespace->name] = [$namespace->sequence => $namespace];
            }
        }

        return $counters;
    }

    /**
     * @param ClassNode $definition
     * @param array     $counters
     */
    private function resolveImportsNamespaces(ClassNode $definition, array $counters): void
    {
        if (!$definition->hasConstructor && $definition->constructorParams) {
            foreach ($definition->constructorParams as $param) {
                //no type (or type is internal), do nothing
                if (empty($param->type) || $param->isBuiltIn()) {
                    continue;
                }

                $namespace = $this->parseNamespaceFromType($param->type);
                if (isset($counters[$namespace->name])) {
                    if ($this->getAlreadyImportedNamespace($counters[$namespace->name], $namespace)) {
                        continue;
                    }

                    $sequence = $this->sequences->find(array_keys($counters[$namespace->name]), $namespace->sequence);
                    if ($sequence !== $namespace->sequence) {
                        $namespace->sequence = $sequence;

                        $param->type->alias = $namespace->fullName();
                    }

                    $counters[$namespace->name][$sequence] = $namespace;
                } else {
                    $counters[$namespace->name] = [$namespace->sequence => $namespace];
                }
            }
        }

        foreach ($definition->dependencies as $dependency) {
            $namespace = $this->parseNamespaceFromType($dependency->type);
            if (isset($counters[$namespace->name])) {
                $alreadyImported = $this->getAlreadyImportedNamespace($counters[$namespace->name], $namespace);
                if ($alreadyImported !== null) {
                    $dependency->type->alias = $alreadyImported->fullName();

                    continue;
                }

                $sequence = $this->sequences->find(array_keys($counters[$namespace->name]), $namespace->sequence);
                if ($sequence !== $namespace->sequence) {
                    $namespace->sequence = $sequence;

                    $dependency->type->alias = $namespace->fullName();
                }

                $counters[$namespace->name][$sequence] = $namespace;
            } else {
                $counters[$namespace->name] = [$namespace->sequence => $namespace];
            }
        }
    }

    /**
     * @param Namespace_[] $counters
     * @param Namespace_   $namespace
     * @return Namespace_|null
     */
    private function getAlreadyImportedNamespace(array $counters, Namespace_ $namespace): ?Namespace_
    {
        foreach ($counters as $counter) {
            if ($counter->equals($namespace)) {
                return $counter;
            }
        }

        return null;
    }

    /**
     * @param ClassNode\Type $type
     * @return Namespace_
     */
    private function parseNamespaceFromType(ClassNode\Type $type): Namespace_
    {
        return $this->parseNamespace($type->shortName, $type->fullName ?? $type->shortName);
    }

    /**
     * @param string $shortName
     * @param string $fullName
     * @return Namespace_
     */
    private function parseNamespace(string $shortName, string $fullName): Namespace_
    {
        if (preg_match("/\d+$/", $shortName, $match)) {
            $sequence = (int)$match[0];
            if ($sequence > 0) {
                return Namespace_::createWithSequence(
                    Utils::trimTrailingDigits($shortName, $sequence),
                    $fullName,
                    $sequence
                );
            }
        }

        return Namespace_::create($shortName, $fullName);
    }
}
