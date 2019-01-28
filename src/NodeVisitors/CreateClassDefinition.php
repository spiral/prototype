<?php

namespace Spiral\Prototype\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Spiral\Prototype\ClassDefinition;

/**
 * Pick class's namespace, name, imports and object instantiations.
 */
class CreateClassDefinition extends NodeVisitorAbstract
{
    /** @var ClassDefinition */
    private $definition;

    public function __construct(ClassDefinition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param Node $node
     *
     * @return int|null|Node|Node[]
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->definition->namespace = join('\\', $node->name->parts);
        }

        if ($node instanceof Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->definition->addImportUsage(join('\\', $use->name->parts), $use->alias);
            }
        }

        if ($node instanceof Node\Stmt\Class_) {
            $this->definition->class = $node->name->name;

            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Node\Stmt\ClassMethod && $stmt->name == '__construct') {
                    return $stmt;
                }
            }
        }

        if ($node instanceof Node\Name\FullyQualified) {
            $this->definition->addInstantiation($node->toString());
        }

        return null;
    }

    public function getClassDefinition(): ?ClassDefinition
    {
        return $this->definition;
    }
}