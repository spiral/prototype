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

/**
 * Remove PrototypeTrait from the class.
 */
class RemoveTrait extends NodeVisitorAbstract
{
    /**
     * @param Node $node
     * @return int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\TraitUse) {
            return null;
        }

        foreach ($node->traits as $index => $use) {
            if ($use instanceof Node\Name\FullyQualified) {
                if (join('\\', $use->parts) == PrototypeTrait::class) {
                    unset($node->traits[$index]);
                }
            }
        }

        if (empty($node->traits)) {
            return NodeTraverser::REMOVE_NODE;
        }

        return $node;
    }
}