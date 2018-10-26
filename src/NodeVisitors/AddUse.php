<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Builder\Use_;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddUse extends NodeVisitorAbstract
{
    /** @var array */
    private $dependencies;

    /**
     * @param array $dependencies
     */
    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @param Node $node
     * @return int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Namespace_) {
            return null;
        }

        // todo: find the right spot
        foreach ($this->dependencies as $name => $type) {
            array_unshift($node->stmts, $this->buildUse($name, $type));
        }
    }

    private function buildUse(string $name, string $type)
    {
        $b = new Use_(
            new Node\Name\FullyQualified($type),
            Node\Stmt\Use_::TYPE_NORMAL
        );

        return $b->getNode();
    }
}