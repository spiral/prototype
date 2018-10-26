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
use PhpParser\NodeVisitorAbstract;

class AddProperty extends NodeVisitorAbstract
{
    /** @var array */
    private $dependencies;

    public function __construct(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return null;
        }

        // todo: find the right spot
        foreach ($this->dependencies as $name => $type) {
            array_unshift($node->stmts, $this->buildProperty($name, $type));
        }
    }

    private function buildProperty(string $name, string $type)
    {
        $b = new Property($name);
        $b->makeProtected();
        $b->setDocComment($this->buildDoc($type));

        return $b->getNode();
    }

    private function buildDoc(string $type): Doc
    {
        $name = substr($type, strrpos($type, '\\') + 1);

        return new Doc("/** @var {$name} */");
    }
}