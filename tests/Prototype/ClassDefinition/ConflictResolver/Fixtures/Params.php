<?php

namespace Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures;

class Params
{
    /**
     * @return \ReflectionParameter[]
     */
    public static function getParams(): array
    {
        try {
            $rc = new \ReflectionClass(self::class);
            $method = $rc->getMethod('paramsSource');

            return $method->getParameters();
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    private function paramsSource(Test $t1, Test $t4, ?TestAlias $a1, SubFolder\Test $st = null, string $t2 = 'value')
    {
    }
}