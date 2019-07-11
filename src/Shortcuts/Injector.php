<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Shortcuts;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\CloningVisitor;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\PrettyPrinterAbstract;
use Spiral\Prototype\NodeVisitors\Shortcuts\AddShortcut;

/**
 * Injects needed shortcuts into a bootloader
 */
final class Injector
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
        if ($lexer === null) {
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
     * Inject shortcut into PHP Class source code. Attention, resulted code will attempt to
     * preserve formatting but will affect it. Do not forget to add formatting fixer.
     *
     * @param string $code
     * @param string $shortcut
     * @param string $binding
     * @param string $constName
     * @param bool   $useConst
     * @return string
     */
    public function injectShortcut(string $code, string $shortcut, string $binding, string $constName, bool $useConst): string
    {
        $tr = new NodeTraverser();
        $tr->addVisitor(new AddShortcut($shortcut, $binding, $constName, $useConst));

        $nodes = $this->parser->parse($code);
        $tokens = $this->lexer->getTokens();

        $output = $tr->traverse($this->cloner->traverse($nodes));

        return $this->printer->printFormatPreserving($output, $nodes, $tokens);
    }
}