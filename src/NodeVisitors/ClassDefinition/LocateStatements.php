<?php

namespace Spiral\Prototype\NodeVisitors\ClassDefinition;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Pick class's namespace, name, imports and object instantiations.
 */
class LocateStatements extends NodeVisitorAbstract
{
    private $imports = [];
    private $instantiations = [];

    /**
     * @param Node $node
     *
     * @return int|null|Node|Node[]
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->imports[] = [
                    'name'  => join('\\', $use->name->parts),
                    'alias' => $use->alias
                ];
            }
        }

        if ($node instanceof Node\Stmt\Class_) {
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassMethod && $stmt->name == '__construct') {
                    return $stmt;
                }
            }
        }

        if ($node instanceof Node\Name\FullyQualified) {
            $this->instantiations[] = $node->toString();
        }

        return null;
    }

    public function getImports(): array
    {
        return $this->imports;
    }

    public function getInstantiations(): array
    {
        return $this->instantiations;
    }
}