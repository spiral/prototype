<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use Spiral\Prototyping\NodeVisitors\LocaleProperties;

class Extractor
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser|null $parser
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?? (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    /**
     * Get list of all virtual property names.
     *
     * @param string $filename
     * @return array
     */
    public function getPrototypedDependencies(string $filename): array
    {
        $v = new LocaleProperties();

        $tr = new NodeTraverser();
        $tr->addVisitor($v);

        $tr->traverse($this->parser->parse(file_get_contents($filename)));

        return $v->getDependencies();
    }
}