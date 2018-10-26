<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

/**
 * Common visitor functionality.
 */
abstract class AbstractVisitor extends NodeVisitorAbstract
{
    /**
     * Create short name (without namespaces).
     *
     * @param string $type
     * @return Name
     */
    protected function buildShortName(string $type): Name
    {
        return new Name(substr($type, strrpos($type, '\\') + 1));
    }

    /**
     * Inject Child node at given index.
     *
     * @param Node\Stmt\Class_ $class
     * @param int              $index
     * @param Node             $child
     * @return Node
     */
    protected function injectNode(Node\Stmt\Class_ $class, int $index, Node $child): Node
    {
        $before = array_slice($class->stmts, 0, $index);
        $after = array_slice($class->stmts, $index);

        $class->stmts = array_merge($before, [$child], $after);

        return $class;
    }
}