<?php

namespace Spiral\Prototype\Tests\ClassDefinition\ConflictResolver;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\ClassDefinition\ConflictResolver\Names;
use Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures;
use Spiral\Prototype\Tests\Fixtures\Dependencies;

class NamesTest extends TestCase
{
    /**
     * @dataProvider cdProvider
     *
     * @param string $method
     * @param array  $vars
     * @param array  $dependencies
     * @param array  $expected
     */
    public function testFind(string $method, array $vars, array $dependencies, array $expected)
    {
        $cd = ClassDefinition::createEmpty();
        $cd->constructorVars = $vars;

        foreach (Fixtures\Params::getParams($method) as $param) {
            $cd->addParam($param);
        }

        $cd->dependencies = Dependencies::convert($dependencies);
        $this->names()->resolve($cd);

        $resolved = [];
        foreach ($cd->dependencies as $dependency) {
            $resolved[] = $dependency->var;
        }

        $this->assertEquals($expected, $resolved);
    }

    public function cdProvider(): array
    {
        return [
            [
                //no conflicts
                'paramsSource',
                [],
                ['v2' => 'type1', 'v' => 'type2', 'vv' => 'type3',],
                ['v2', 'v', 'vv']
            ],
            [
                //has conflicts
                'paramsSource',
                ['v', 'v2'],
                ['v2' => 'type1', 'v' => 'type2', 'vv' => 'type3', 't1' => 'type4', 't2' => 'type4', 't4' => 'type4', 't6' => 'type4'],
                ['v3', 'v4', 'vv', 't', 't2', 't3', 't6']
            ],
            [
                //has conflicts
                'paramsSource2',
                ['v', 'v2'],
                ['v2' => 'type1', 'v' => 'type2', 'vv' => 'type3', 't1' => 'type4', 't2' => 'type4', 't4' => 'type4', 't6' => 'type4'],
                ['v3', 'v4', 'vv', 't', 't3', 't5', 't6']
            ],
        ];
    }

    private function names(): Names
    {
        $container = new Container();

        return $container->get(Names::class);
    }
}