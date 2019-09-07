<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Command;

use Spiral\Prototype\Annotation;
use Spiral\Prototype\Traits\PrototypeTrait;
use Symfony\Component\Console\Input\InputOption;

final class DumpCommand extends AbstractCommand
{
    public const NAME        = 'prototype:dump';
    public const DESCRIPTION = 'Dump all prototyped dependencies as PrototypeTrait docComment.';
    public const OPTIONS     = [
        ['render', 'r', InputOption::VALUE_NONE, 'Render PrototypeTrait DocComment']
    ];

    /**
     * Show list of available shortcuts and update trait docComment.
     */
    public function perform()
    {
        $dependencies = $this->registry->getPrototypeDependencies();
        if ($dependencies === []) {
            $this->writeln('<comment>No prototyped shortcuts found.</comment>');
            return;
        }

        $grid = $this->table(['Property:', 'Target:']);

        foreach ($dependencies as $dependency) {
            $grid->addRow([$dependency->var, $dependency->type->fullName]);
        }

        $grid->render();

        if (!$this->option('render')) {
            return;
        }

        $this->write("Updating <fg=yellow>PrototypeTrait</fg=yellow> DocComment... ");

        $trait = new \ReflectionClass(PrototypeTrait::class);
        $docComment = $trait->getDocComment();
        if ($docComment === false) {
            $this->write("<fg=reg>DocComment is missing</fg=red>");
            return;
        }

        $filename = $trait->getFileName();

        file_put_contents(
            $filename,
            str_replace(
                $docComment,
                $this->buildAnnotation($dependencies),
                file_get_contents($filename)
            )
        );

        $this->write("<fg=green>complete</fg=green>");
    }

    /**
     * @param array $dependencies
     * @return string
     */
    private function buildAnnotation(array $dependencies): string
    {
        $an = new Annotation\Parser('');
        $an->lines[] = new Annotation\Line(
            'This DocComment is auto-generated, do not edit or commit this file to repository.'
        );
        $an->lines[] = new Annotation\Line('');

        foreach ($dependencies as $dependency) {
            $an->lines[] = new Annotation\Line(
                sprintf('\\%s $%s', $dependency->type->fullName, $dependency->var),
                'property'
            );
        }

        return $an->compile();
    }
}