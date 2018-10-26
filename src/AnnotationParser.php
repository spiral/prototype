<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

namespace Spiral\Prototyping;

/**
 * Simple annotation parser and compiler.
 */
class AnnotationParser
{
    /** @var AnnotationLine[] */
    public $lines = [];

    /**
     * @param string $comment
     */
    public function __construct(string $comment)
    {
        $lines = explode("\n", $comment);

        foreach ($lines as $line) {
            $line = ltrim(trim($line, "\r "), "\t*/\\ ");

            if (preg_match('/@([^ ]+) (.*)/iu', $line, $matches)) {
                $this->lines[] = new AnnotationLine($matches[2], $matches[1]);
            } else {
                $this->lines[] = new AnnotationLine($line);
            }
        }

        if (isset($this->lines[0]) && $this->lines[0]->value == "") {
            array_shift($this->lines);
        }

        if (isset($this->lines[count($this->lines) - 1]) && $this->lines[count($this->lines) - 1]->value == "") {
            array_pop($this->lines);
        }
    }

    /**
     * @return string
     */
    public function compile(): string
    {
        $result = [];
        $result[] = '/**';

        // skip first and last tokens
        foreach ($this->lines as $line) {
            if ($line->type == null) {
                $result[] = sprintf(' * %s', $line->value);
                continue;
            }

            $result[] = sprintf(' * @%s %s', $line->type, $line->value);
        }

        $result[] = ' */';

        return join("\n", $result);
    }
}