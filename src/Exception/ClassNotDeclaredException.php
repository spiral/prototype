<?php

namespace Spiral\Prototype\Exception;

class ClassNotDeclaredException extends \Exception
{
    public function __construct(string $filename)
    {
        parent::__construct("Class declaration not found in \"$filename\" directory.");
    }
}