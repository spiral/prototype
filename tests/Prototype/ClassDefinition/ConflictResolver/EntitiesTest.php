<?php

namespace Spiral\Prototype\Tests\ClassDefinition\ConflictResolver;

use PHPUnit\Framework\TestCase;
use Spiral\Prototype\ClassDefinition\ConflictResolver\Name_;

class EntitiesTest extends TestCase
{
    /**
     * @dataProvider nameProvider
     *
     * @param string $name
     * @param int    $sequence
     * @param string $expected
     */
    public function testName(string $name, int $sequence, string $expected)
    {
        $this->assertEquals($expected, Name_::createWithSequence($name, $sequence)->fullName());
    }

    public function nameProvider(): array
    {
        return [
            ['name', 7, 'name7'],
            ['name', 0, 'name'],
            ['name', -1, 'name'],
            ['name', 1, 'name1'],
        ];
    }
}