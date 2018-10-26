<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Builder\Param;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class UpConstructorVisitor extends NodeVisitorAbstract
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

        foreach ($node->stmts as $ch) {
            if ($ch instanceof Node\Stmt\ClassMethod) {
                // found first method
                //    dump($ch);
            }
        }

        $constructor = $this->getConstructor($node);

        // inject params
        // todo: modify doc comment (need doc comment parser)
        foreach ($this->dependencies as $name => $type) {
            array_unshift(
                $constructor->params,
                (new Param($name))->setType(
                    new Node\Name($this->shortName($type))
                )->getNode()
            );

            $prop = new Node\Expr\PropertyFetch(new Node\Expr\Variable("this"), $name);

            array_unshift($constructor->stmts, new Node\Stmt\Expression(
                new Node\Expr\Assign($prop, new Node\Expr\Variable($name))
            ));
        }

        // fix doc comment ? or move to another one
        // define constructor to be moved?
    }

    private function getConstructor(Node\Stmt\Class_ $node)
    {
        $constructor = new Node\Stmt\ClassMethod("__constructor");
        $constructor->flags = BuilderHelpers::addModifier(
            $constructor->flags,
            Node\Stmt\Class_::MODIFIER_PUBLIC
        );

        // todo: use existed if found
        array_unshift($node->stmts, $constructor);

        return $constructor;
    }


    private function shortName(string $type): string
    {
        return substr($type, strrpos($type, '\\') + 1);
    }

}