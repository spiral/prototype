<?php
declare(strict_types=1);

namespace Spiral\Prototype\NodeVisitors\ClassDefinition;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class LocateVariables extends NodeVisitorAbstract
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