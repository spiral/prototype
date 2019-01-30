<?php

namespace Spiral\Prototype\Tests\ClassDefinition\ConflictResolver;

use PHPUnit\Framework\TestCase;
use Spiral\Core\Container;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\ClassDefinition\ConflictResolver\Namespaces;
use Spiral\Prototype\Tests\ClassDefinition\ConflictResolver\Fixtures;
use Spiral\Prototype\Tests\Fixtures\Dependencies;

class NamespacesTest extends TestCase
{
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @dataProvider cdProvider
     *
     * @param array $stmts
     * @param array $dependencies
     * @param array $expected
     */
    public function testFind(array $stmts, array $dependencies, array $expected)
    {
        $cd = ClassDefinition::create('class\name');

        foreach ($stmts as $alias => $name) {
            $cd->addImportUsage($name, $alias);
        }

        foreach (Fixtures\Params::getParams('paramsSource') as $param) {
            $cd->addParam($param);
        }

        $cd->dependencies = Dependencies::convert($dependencies);
        $this->namespaces()->resolve($cd);

        $resolved = [];
        foreach ($cd->dependencies as $dependency) {
            $resolved[$dependency->property] = $dependency->type->getAliasOrShortName();
        }

        $this->assertEquals($expected, $resolved);
    }

    public function cdProvider(): array
    {
        return [
            [
                //no conflicts
                [],
                [
                    'v1' => 'a\\b\\c\\type1',
                    'v2' => 'a\\b\\type2',
                    'v3' => 'a\\b\\c\\type3'
                ],
                [
                    'v1' => 'type1',
                    'v2' => 'type2',
                    'v3' => 'type3'
                ]
            ],
            [
                [
                    'Test'      => 'a\\b\\Test',
                    'TestAlias' => 'a\\b\\Test',
                    'type1'     => 'a\\b\\c\\type1',
                    'type2'     => 'a\\b\\c\\type2',
                    'type7'     => 'a\\b\\c\\type4',
                ],
                //has conflicts
                [
                    'v1' => 'a\\b\\c\\type1',
                    'v2' => 'a\\b\\type1',
                    'v3' => 'a\\b\\c\\type2',
                    'v4' => 'a\\b\\type2',
                    'v5' => 'a\\b\\c\\type7',
                    'v6' => 'a\\b\\c\\type4',
                    'v7' => 'a\\b\\type4',
                    'v8' => 'a\\b\\type5',
                ],
                [
                    'v1' => 'type1',
                    'v2' => 'type',
                    'v3' => 'type2',
                    'v4' => 'type3',
                    'v5' => 'type4',
                    'v6' => 'type7',
                    'v7' => 'type5',
                    'v8' => 'type6',
                ]
            ],
            [
                [],
                //has conflicts
                [
                    'v1' => 'a\\b\\type',
                    'v2' => 'a\\b\\c\\type',
                    'v3' => 'a\\b\\c\\type3',
                ],
                [
                    'v1' => 'type',
                    'v2' => 'type2',
                    'v3' => 'type3',
                ]
            ],
        ];
    }

    private function namespaces(): Namespaces
    {
        $container = new Container();

        return $container->get(Namespaces::class);
    }
}