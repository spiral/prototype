<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Builder\Property;
use PhpParser\Comment\Doc;
use PhpParser\Node;

class AddProperty extends AbstractVisitor
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
        if (!$node instanceof Node\Stmt\Class_) {
            return null;
        }

        $placementID = 0;
        foreach ($node->stmts as $index => $child) {
            $placementID = $index;
            if ($child instanceof Node\Stmt\ClassMethod) {
                break;
            }
        }

        $nodes = [];
        foreach ($this->dependencies as $name => $type) {
            $nodes[] = $this->buildProperty($name, $type);
        }

        $node->stmts = $this->injectValues($node->stmts, $placementID, $nodes);

        return $node;
    }

    /**
     * @param string $name
     * @param string $type
     * @return Node|Node\Stmt\Property
     */
    private function buildProperty(string $name, string $type): Node\Stmt\Property
    {
        $b = new Property($name);
        $b->makeProtected();
        $b->setDocComment(new Doc(sprintf("/** @var %s */", $this->shortName($type))));

        return $b->getNode();
    }
}