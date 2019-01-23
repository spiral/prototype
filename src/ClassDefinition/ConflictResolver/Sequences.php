<?php

namespace Spiral\Prototype\ClassDefinition\ConflictResolver;

class Sequences
{
    public function find(array $names, int $originSequence): int
    {
        if (empty($names)) {
            return 0;
        }

        $sequences = array_keys($names);
        $skipped = $this->skippedSequences($sequences);

        if (isset($skipped[$originSequence])) {
            return $originSequence;
        }

        if (isset($skipped[1]) && isset($sequences[0])) {
            //we do not add "1" as postfix: $var, $var2, $var3, etc
            unset($skipped[1]);
        }

        if (empty($skipped)) {
            return max($sequences) + 1;
        }

        return current($skipped);
    }

    private function skippedSequences(array $sequences): array
    {
        $skipped = [];
        for ($i = 0; $i < max($sequences); $i++) {
            if (!in_array($i, $sequences)) {
                $skipped[$i] = $i;
            }
        }

        return $skipped;
    }
}