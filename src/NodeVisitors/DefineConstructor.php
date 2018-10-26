<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\BuilderHelpers;
use PhpParser\Node;

/**
 * Ensure correct placement and presence of __constructor.
 */
class DefineConstructor extends AbstractVisitor
{
    /**
     * @param Node $node
     * @return int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return null;
        }

        $placementID = 0;

        // seeking for the correct placement
        foreach ($node->stmts as $index => $child) {
            $placementID = $index;
            if ($child instanceof Node\Stmt\ClassMethod) {
                // found existed one
                if ($child->name->name == '__constructor') {
                    $node->setAttribute('constructor', $child);
                    return null;
                }

                // first method declaration in a class
                break;
            }
        }

        $constructor = $this->buildConstructor();
        $node->setAttribute('constructor', $constructor);

        return $this->injectNode($node, $placementID, $constructor);
    }

    /**
     * @return Node\Stmt\ClassMethod
     */
    private function buildConstructor(): Node\Stmt\ClassMethod
    {
        $constructor = new Node\Stmt\ClassMethod("__constructor");
        $constructor->flags = BuilderHelpers::addModifier(
            $constructor->flags,
            Node\Stmt\Class_::MODIFIER_PUBLIC
        );

        return $constructor;
    }
}