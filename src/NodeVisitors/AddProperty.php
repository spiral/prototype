<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototype\NodeVisitors;

use PhpParser\Builder\Property;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\Dependency;
use Spiral\Prototype\Utils;

class AddProperty extends NodeVisitorAbstract
{
    /** @var ClassDefinition */
    private $definition;

    /**
     * @param ClassDefinition $definition
     */
    public function __construct(ClassDefinition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * @param Node $node
     *
     * @return int|null|Node|Node[]
     */
    public function leaveNode(Node $node)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return null;
        }

        $nodes = [];
        foreach ($this->definition->dependencies as $dependency) {
            $nodes[] = $this->buildProperty($dependency);
        }

        $placementID = $this->definePlacementID($node);
        $node->stmts = Utils::injectValues($node->stmts, $placementID, $nodes);

        return $node;
    }

    private function definePlacementID(Node\Stmt\Class_ $node): int
    {
        foreach ($node->stmts as $index => $child) {
            if ($child instanceof Node\Stmt\ClassMethod || $child instanceof Node\Stmt\Property) {
                return $index;
            }
        }

        return 0;
    }

    private function buildProperty(Dependency $dependency): Node\Stmt\Property
    {
        $b = new Property($dependency->property);
        $b->makeProtected();
        $b->setDocComment(new Doc(sprintf("/** @var %s */", $this->getPropertyType($dependency))));

        return $b->getNode();
    }

    private function getPropertyType(Dependency $dependency): string
    {
        foreach ($this->definition->getStmts() as $stmt) {
            if ($stmt->name === $dependency->type->fullName) {
                if ($stmt->alias) {
                    return $stmt->alias;
                }
            }
        }

        return $dependency->type->getAliasOrShortName();
    }
}