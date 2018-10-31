<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Node;
use PhpParser\Node\Stmt;

/**
 * Ensures that needed nodes indicate the need for an extra line after them.
 */
class NewLine extends AbstractVisitor
{
    /**
     * @param Node $node
     * @return int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Namespace_ && !$node instanceof Node\Stmt\Class_) {
            return null;
        }

        foreach ($node->stmts as $index => $child) {
            if (isset($node->stmts[$index + 1]) && $this->nlRequired($child, $node->stmts[$index + 1])) {
                $child->setAttribute("nl", true);
            }
        }

        return $node;
    }

    /**
     * Return true if new line is required after the node.
     *
     * @param Node $node
     * @param Node $next
     * @return bool
     */
    private function nlRequired(Node $node, Node $next): bool
    {
        // Always space after the last use
        if ($node instanceof Stmt\Use_ && !$next instanceof Stmt\Use_) {
            return true;
        }

        // new line after the group of uses
        if ($node instanceof Stmt\TraitUse && !$next instanceof Stmt\TraitUse) {
            return true;
        }

        // new line after the group of const(s)
        if ($node instanceof Stmt\Const_ && !$next instanceof Node\Const_) {
            return true;
        }

        // always new line after the property and method declarations
        if ($node instanceof Stmt\Property || $node instanceof Stmt\ClassMethod) {
            return true;
        }

        return false;
    }
}