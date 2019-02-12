<?php
declare(strict_types=1);

namespace Spiral\Prototype\ClassDefinition;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use Spiral\Prototype\Annotation\Parser;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\ClassDefinition\ConflictResolver;
use Spiral\Prototype\Exception\ClassNotDeclaredException;
use Spiral\Prototype\NodeVisitors\ClassDefinition\LocateStatements;
use Spiral\Prototype\NodeVisitors\ClassDefinition\DeclareClass;
use Spiral\Prototype\NodeVisitors\ClassDefinition\LocateVariables;

class Extractor
{
    /** @var Parser */
    private $parser;

    /** @var ConflictResolver\Names */
    private $namesResolver;

    /** @var ConflictResolver\Namespaces */
    private $namespacesResolver;

    public function __construct(ConflictResolver\Names $namesResolver, ConflictResolver\Namespaces $namespacesResolver, Parser $parser = null)
    {
        $this->namesResolver = $namesResolver;
        $this->namespacesResolver = $namespacesResolver;
        $this->parser = $parser ?? (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    /**
     * @param string $filename
     * @param array  $dependencies
     *
     * @return ClassDefinition
     * @throws ClassNotDeclaredException
     */
    public function extract(string $filename, array $dependencies): ClassDefinition
    {
        $definition = $this->makeDefinition($filename);
        $definition->dependencies = $dependencies;

        $stmts = new LocateStatements();
        $vars = new LocateVariables();
        $this->traverse($filename, $stmts, $vars);

        $this->fillStmts($definition, $stmts->getImports());
        $this->fillConstructorParams($definition);
        $this->fillConstructorVars($vars->getVars(), $definition);
        $this->resolveConflicts($definition);

        return $definition;
    }

    /**
     * @param string $filename
     *
     * @return ClassDefinition
     * @throws ClassNotDeclaredException
     */
    private function makeDefinition(string $filename): ClassDefinition
    {
        $declarator = new DeclareClass();
        $this->traverse($filename, $declarator);

        if (empty($declarator->getClass())) {
            throw new ClassNotDeclaredException($filename);
        }

        if ($declarator->getNamespace()) {
            return ClassDefinition::createWithNamespace($declarator->getClass(), $declarator->getNamespace());
        }

        return ClassDefinition::create($declarator->getClass());
    }

    private function traverse(string $filename, NodeVisitor ...$visitors)
    {
        $tr = new NodeTraverser();

        foreach ($visitors as $visitor) {
            $tr->addVisitor($visitor);
        }

        $tr->traverse($this->parser->parse(file_get_contents($filename)));
    }

    private function fillStmts(ClassDefinition $definition, array $imports)
    {
        foreach ($imports as $import) {
            $definition->addImportUsage($import['name'], $import['alias']);
        }
    }

    private function fillConstructorParams(ClassDefinition $definition)
    {
        $reflection = new \ReflectionClass("{$definition->namespace}\\{$definition->class}");

        $constructor = $reflection->getConstructor();
        if (!empty($constructor)) {
            $definition->hasConstructor = $constructor->getDeclaringClass()->getName() === $reflection->getName();

            foreach ($reflection->getConstructor()->getParameters() as $parameter) {
                $definition->addParam($parameter);
            }
        }
    }

    /**
     * Collect all variable definitions from constructor method body.
     * Vars which are however also inserted via method are ignored (and still used as constructor params).
     *
     * @param array           $vars
     * @param ClassDefinition $definition
     */
    private function fillConstructorVars(array $vars, ClassDefinition $definition)
    {
        foreach ($vars as $k => $var) {
            if (isset($definition->constructorParams[$var])) {
                unset($vars[$k]);
            }
        }

        $definition->constructorVars = $vars;
    }

    private function resolveConflicts(ClassDefinition $definition)
    {
        $this->namesResolver->resolve($definition);
        $this->namespacesResolver->resolve($definition);
    }
}