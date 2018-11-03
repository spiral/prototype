<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping\NodeVisitors;

use PhpParser\Builder\Param;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use Spiral\Prototyping\Annotation;

/**
 * Injects new constructor dependencies and modifies comment.
 */
class UpdateConstructor extends AbstractVisitor
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

        /** @var Node\Stmt\ClassMethod $constructor */
        $constructor = $node->getAttribute('constructor');

        $this->addDependencies($constructor);

        $constructor->setDocComment(
            $this->addComments($constructor->getDocComment())
        );
    }

    /**
     * Add dependencies to constructor method.
     *
     * @param Node\Stmt\ClassMethod $constructor
     */
    private function addDependencies(Node\Stmt\ClassMethod $constructor)
    {
        foreach ($this->dependencies as $name => $type) {
            array_unshift(
                $constructor->params,
                (new Param($name))->setType(new Node\Name($this->shortName($type)))->getNode()
            );

            $prop = new Node\Expr\PropertyFetch(new Node\Expr\Variable("this"), $name);

            array_unshift(
                $constructor->stmts,
                new Node\Stmt\Expression(new Node\Expr\Assign($prop, new Node\Expr\Variable($name)))
            );
        }
    }

    /**
     * Add PHPDoc comments into __constructor.
     *
     * @param Doc|null $doc
     * @return Doc
     */
    private function addComments(Doc $doc = null): Doc
    {
        $an = new Annotation\Parser($doc ? $doc->getText() : "");

        $params = [];
        foreach ($this->dependencies as $name => $type) {
            $params[] = new Annotation\Line(
                sprintf('%s $%s', $this->shortName($type), $name),
                'param'
            );
        }

        $placementID = 0;
        $previous = null;
        foreach ($an->lines as $index => $line) {
            // always next node
            $placementID = $index + 1;

            // inject before this parameters
            if ($line->is(['param', 'throws', 'return'])) {
                // insert before given node
                $placementID--;
                break;
            }

            $previous = $line;
        }

        if (!empty($previous) && !$previous->isEmpty()) {
            $an->lines = $this->injectValues($an->lines, $placementID, [new Annotation\Line("")]);
            $placementID++;
        }

        $an->lines = $this->injectValues($an->lines, $placementID, $params);
        return new Doc($an->compile());
    }
}