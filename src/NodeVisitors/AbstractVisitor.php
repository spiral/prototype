<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\NodeVisitors;

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
    protected function shortName(string $type): Name
    {
        return new Name(substr($type, strrpos($type, '\\') + 1));
    }

    /**
     * Inject values to array at given index.
     *
     * @param array $stmts
     * @param int   $index
     * @param array $child
     * @return array
     */
    protected function injectValues(array $stmts, int $index, array $child): array
    {
        $before = array_slice($stmts, 0, $index);
        $after = array_slice($stmts, $index);

        return array_merge($before, $child, $after);
    }
}