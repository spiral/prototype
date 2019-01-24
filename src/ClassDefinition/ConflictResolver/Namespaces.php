<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Utils;

class Namespaces
{
    /** @var Sequences */
    private $sequences;

    public function __construct(Sequences $sequences)
    {
        $this->sequences = $sequences;
    }

    public function resolve(ClassDefinition $definition)
    {
        $namespaces = $this->getReservedNamespaces($definition);
        $counters = $this->initiateCounters($namespaces);

        $this->resolveImportsNamespaces($definition, $counters);
    }

    private function getReservedNamespaces(ClassDefinition $definition)
    {
        $names = [];
        foreach ($definition->getStmts() as $stmt) {
            if (!$stmt->alias) {
                continue;
            }

            $names[$stmt->alias] = $stmt->name;
        }

        foreach ($definition->getStmts() as $stmt) {
            if ($stmt->alias || !$stmt->imported) {
                continue;
            }

            if (!isset($names[$stmt->shortName])) {
                $names[$stmt->shortName] = $stmt->name;
            }
        }

        return $names;
    }

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

    private function resolveImportsNamespaces(ClassDefinition $definition, array $counters)
    {
        if (!$definition->hasConstructor && $definition->constructorParams) {
            foreach ($definition->constructorParams as $param) {
                //no type (or type is internal), do nothing
                if (empty($param->type) || $param->isBuiltIn()) {
                    continue;
                }

                $namespace = $this->parseNamespaceFromType($param->type);
                if (isset($counters[$namespace->name])) {
                    if ($this->sameNamespace($counters[$namespace->name], $namespace)) {
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
                if ($this->sameNamespace($counters[$namespace->name], $namespace)) {
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
     *
     * @return bool
     */
    private function sameNamespace(array $counters, Namespace_ $namespace): bool
    {
        foreach ($counters as $counter) {
            if ($counter->equals($namespace)) {
                return true;
            }
        }

        return false;
    }

    private function parseNamespaceFromType(ClassDefinition\Type $type): Namespace_
    {
        return $this->parseNamespace($type->shortName, $type->fullName ?? $type->shortName);
    }

    private function parseNamespace(string $shortName, string $fullName): Namespace_
    {
        if (preg_match("/\d+$/", $shortName, $match)) {
            $sequence = (int)$match[0];
            if ($sequence > 0) {
                return Namespace_::createWithSequence(Utils::trimTrailingDigits($shortName, $sequence), $fullName, $sequence);
            }
        }

        return Namespace_::create($shortName, $fullName);
    }
}