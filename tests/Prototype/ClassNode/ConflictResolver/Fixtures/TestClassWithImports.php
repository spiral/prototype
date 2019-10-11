<?php

declare(strict_types=1);

namespace Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures;

//this is an alias which should be inserted as a dependency type
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures\Test as FTest;
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures\SubFolder\Test as TestAlias;
//
use Spiral\Prototype\Tests\ClassNode\ConflictResolver\Fixtures\TestAlias as ATest3;
use Spiral\Prototype\Tests\Fixtures\TestApp as Test;
use Spiral\Prototype\Traits\PrototypeTrait;

class TestClassWithImports
{
}
