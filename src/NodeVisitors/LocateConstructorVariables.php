<?php

namespace Spiral\Prototype\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class LocateConstructorVariables extends NodeVisitorAbstract
{
    private $vars = [];

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
            $this->vars[] = $node->name;
        }
    }

    public function getVars(): array
    {
        return $this->vars;
    }
}