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
use Spiral\Prototyping\AnnotationLine;
use Spiral\Prototyping\AnnotationParser;

class UpdateConstructor extends AbstractVisitor
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
        $an = new AnnotationParser($doc ? $doc->getText() : "");

        $params = [];
        foreach ($this->dependencies as $name => $type) {
            $params[] = new AnnotationLine(
                sprintf('%s $%s', $this->shortName($type), $name),
                'param'
            );
        }

        $placementID = 0;
        foreach ($an->lines as $index => $line) {
            $placementID = $index;

            if ($line->type == 'param' || $line->type == 'throws' || $line->type == 'return') {
                break;
            }
        }

        $an->lines = $this->injectValues($an->lines, $placementID, $params);
        return new Doc($an->compile());
    }
}