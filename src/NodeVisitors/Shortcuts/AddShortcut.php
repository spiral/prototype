<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\NodeVisitors\Shortcuts;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class AddShortcut extends NodeVisitorAbstract
{
    /** @var string */
    private $shortcut;

    /** @var string */
    private $binding;

    /** @var string */
    private $constName;

    /** @var bool */
    private $useConst;

    public function __construct(string $shortcut, string $binding, string $constName, bool $useConst)
    {
        $this->shortcut = $shortcut;
        $this->binding = $binding;
        $this->constName = $constName;
        $this->useConst = $useConst;
    }

    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Const_ &&
            $node->name->name === $this->constName &&
            $node->value instanceof Node\Expr\Array_) {
            $node->value->items[] = new Node\Expr\ArrayItem(
                $this->value(),
                new Node\Scalar\String_($this->shortcut)
            );
        }

        return null;
    }

    private function value(): Node\Expr
    {
        if ($this->useConst) {
            return new Node\Expr\ClassConstFetch(new Node\Name($this->binding), 'class');
        }

        return new Node\Scalar\String_($this->binding);
    }
}
