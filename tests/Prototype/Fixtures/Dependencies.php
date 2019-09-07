<?php

namespace Spiral\Prototype\Tests\Fixtures;

use Spiral\Prototype\Dependency;

class Dependencies
{
    public static function convert(array $deps): array
    {
        $converted = [];
        foreach ($deps as $name => $type) {
            $converted[$name] = Dependency::create($name, $type);
        }

        return $converted;
    }
}