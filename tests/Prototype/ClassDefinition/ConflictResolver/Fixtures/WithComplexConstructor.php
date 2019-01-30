<?php

namespace Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures;

class WithComplexConstructor
{
    public function __construct(
        string $str1,
        $var,
        ATest3 $testApp,
        ?string $str2,
        ?\StdClass $nullableClass1,
        ?Test $test1 = null,
        ?string $str3 = null,
        ?int $int = 123,
        \StdClass $nullableClass2 = null
    ) {
    }
}