<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ExPropertyVisitor extends NodeVisitorAbstract
{
    private $properties = [];

    private $requested = [];

    public function getDependencies(): array
    {
        return array_values(array_diff(
            array_values($this->requested),
            array_values($this->properties)
        ));
    }

    /**
     * Detected declared and requested nodes.
     *
     * @param Node $node
     * @return int|null|Node
     */
    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\PropertyFetch) {
            $this->requested[$node->name->name] = $node->name->name;
        }

        if ($node instanceof Node\Stmt\Property) {
            foreach ($node->props as $prop) {
                if ($prop instanceof Node\Stmt\PropertyProperty) {
                    $this->properties[$prop->name->name] = $prop->name->name;
                }
            }
        }

        return null;
    }
}