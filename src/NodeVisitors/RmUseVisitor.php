<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;


use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use Spiral\Prototyping\Traits\PrototypeTrait;

class RmUseVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Use_) {
            return null;
        }

        foreach ($node->uses as $index => $use) {
            if ($use->name->toString() == PrototypeTrait::class) {
                unset($node->uses[$index]);
            }
        }

        if (empty($node->uses)) {
            return NodeTraverser::REMOVE_NODE;
        }

        return $node;
    }
}