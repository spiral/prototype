<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter\Standard;
use Spiral\Prototyping\NodeVisitors\NewLine;

/**
 * Ensures lines between some declarations. Does not change any other formatting.
 */
class PrettyPrinter extends Standard
{
    /**
     * @param array $nodes
     */
    protected function preprocessNodes(array $nodes)
    {
        parent::preprocessNodes($nodes);

        $tr = new NodeTraverser();
        $tr->addVisitor(new NewLine());
        $tr->traverse($nodes);
    }

    /**
     * @param Node $node
     * @param bool $parentFormatPreserved
     * @return string
     */
    protected function p(Node $node, $parentFormatPreserved = false): string
    {
        $result = parent::p($node, $parentFormatPreserved);

        if ($node->getAttribute("nl")) {
            $result .= $this->nl;
        }

        return rtrim($result, "\n");
    }
}