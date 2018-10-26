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
use Spiral\Prototyping\NodeVisitors\AdPropertyVisitor;
use Spiral\Prototyping\NodeVisitors\AdUseVisitor;
use Spiral\Prototyping\NodeVisitors\RmTraitVisitor;
use Spiral\Prototyping\NodeVisitors\RmUseVisitor;
use Spiral\Prototyping\NodeVisitors\UpConstructorVisitor;

class DependencyInjector
{
    /** @var Parser */
    private $parser;

    /** @var Lexer */
    private $lexer;

    private $printer;

    private $cloner;

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

    public function injectDependencies(string $code, array $dependencies = []): string
    {
        $tr = new NodeTraverser();
        $tr->addVisitor(new NameResolver());
        $tr->addVisitor(new AdUseVisitor($dependencies));
        $tr->addVisitor(new RmUseVisitor());
        $tr->addVisitor(new RmTraitVisitor());
        $tr->addVisitor(new AdPropertyVisitor($dependencies));
        $tr->addVisitor(new UpConstructorVisitor($dependencies));

        $nodes = $this->parser->parse($code);
        $tokens = $this->lexer->getTokens();

        return $this->printer->printFormatPreserving(
            $tr->traverse($this->cloner->traverse($nodes)),
            $nodes,
            $tokens
        );
    }
}