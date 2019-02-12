<?php
declare(strict_types=1);

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Utils;

class Names
{
    /** @var Sequences */
    private $sequences;

    public function __construct(Sequences $sequences)
    {
        $this->sequences = $sequences;
    }

    public function resolve(ClassDefinition $definition)
    {
        $reservedNames = $this->getConstructorReservedNames($definition);
        $counters = $this->initiateCounters($reservedNames);

        $this->addPostfixes($definition, $counters);
    }

    private function getConstructorReservedNames(ClassDefinition $definition): array
    {
        $names = [];
        foreach ($definition->constructorVars as $name) {
            $names[] = $name;
        }

        foreach ($definition->constructorParams as $param) {
            $names[] = $param->name;
        }

        return $names;
    }

    private function initiateCounters(array $names): array
    {
        $counters = [];
        foreach ($names as $name) {
            $name = $this->parseName($name);

            if (isset($counters[$name->name])) {
                $counters[$name->name][$name->sequence] = $name->fullName();
            } else {
                $counters[$name->name] = [$name->sequence => $name->fullName()];
            }
        }

        return $counters;
    }

    private function addPostfixes(ClassDefinition $definition, array $counters)
    {
        foreach ($definition->dependencies as $dependency) {
            $name = $this->parseName($dependency->var);
            if (isset($counters[$name->name])) {
                $sequence = $this->sequences->find(array_keys($counters[$name->name]), $name->sequence);
                if ($sequence !== $name->sequence) {
                    $name->sequence = $sequence;

                    $dependency->var = $name->fullName();
                }

                $counters[$name->name][$sequence] = $name->fullName();
            } else {
                $counters[$name->name] = [$name->sequence => $name->fullName()];
            }
        }
    }

    private function parseName(string $name): Name_
    {
        if (preg_match("/\d+$/", $name, $match)) {
            $sequence = (int)$match[0];
            if ($sequence > 0) {
                return Name_::createWithSequence(Utils::trimTrailingDigits($name, $sequence), $sequence);
            }
        }

        return Name_::create($name);
    }
}