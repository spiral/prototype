<?php
declare(strict_types=1);

namespace Spiral\Prototype\NodeVisitors\ClassDefinition;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Pick class's namespace, name, imports.
 */
class LocateStatements extends NodeVisitorAbstract
{
    private $imports = [];

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
                    'alias' => !empty($use->alias) ? $use->alias->name : null
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

        return null;
    }

    public function getImports(): array
    {
        return $this->imports;
    }
}