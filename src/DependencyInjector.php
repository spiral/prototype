<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use Spiral\Prototyping\NodeVisitors\AddProperty;
use Spiral\Prototyping\NodeVisitors\AddUse;
use Spiral\Prototyping\NodeVisitors\DefineConstructor;
use Spiral\Prototyping\NodeVisitors\RemoveTrait;
use Spiral\Prototyping\NodeVisitors\RemoveUse;
use Spiral\Prototyping\NodeVisitors\UpdateConstructor;

/**
 * Injects needed class dependencies into given source code.
 */
class DependencyInjector
{
    /** @var Parser */
    private $parser;

    /** @var Lexer */
    private $lexer;

    /** @var null|Standard|PrettyPrinterAbstract */
    private $printer;

    /** @var NodeTraverser */
    private $cloner;

    /**
     * @param Lexer|null                 $lexer
     * @param PrettyPrinterAbstract|null $printer
     */
    public function __construct(Lexer $lexer = null, PrettyPrinterAbstract $printer = null)
    {
        if (empty($lexer)) {
            $lexer = new Lexer\Emulative([
                'usedAttributes' => [
                    'comments',
                    'startLine',
                    'endLine',
                    'startTokenPos',
                    'endTokenPos',
                ],
            ]);
        }

        $this->lexer = $lexer;
        $this->parser = new Parser\Php7($this->lexer);

        $this->cloner = new NodeTraverser();
        $this->cloner->addVisitor(new CloningVisitor());

        $this->printer = $printer ?? new Standard();
    }

    /**
     * @param string $code
     * @param array  $dependencies
     * @return string
     */
    public function injectDependencies(string $code, array $dependencies = []): string
    {
        $tr = new NodeTraverser();
        $tr->addVisitor(new NameResolver());
        $tr->addVisitor(new AddUse($dependencies));
        $tr->addVisitor(new RemoveUse());
        $tr->addVisitor(new RemoveTrait());
        $tr->addVisitor(new AddProperty($dependencies));
        $tr->addVisitor(new DefineConstructor());
        $tr->addVisitor(new UpdateConstructor($dependencies));

        $nodes = $this->parser->parse($code);
        $tokens = $this->lexer->getTokens();

        return $this->printer->printFormatPreserving(
            $tr->traverse($this->cloner->traverse($nodes)),
            $nodes,
            $tokens
        );
    }
}