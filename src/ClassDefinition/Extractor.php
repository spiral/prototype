<?php

namespace Spiral\Prototype\ClassDefinition;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\ParserFactory;
use Spiral\Prototype\Annotation\Parser;
use Spiral\Prototype\ClassDefinition;
use Spiral\Prototype\ClassDefinition\ConflictResolver;
use Spiral\Prototype\Dependency;
use Spiral\Prototype\NodeVisitors\CreateClassDefinition;
use Spiral\Prototype\NodeVisitors\LocateConstructorVariables;

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
     * @param string       $code
     * @param Dependency[] $dependencies
     *
     * @return ClassDefinition
     */
    public function extract(string $code, array $dependencies): ClassDefinition
    {
        $def = new CreateClassDefinition(ClassDefinition::createEmpty());
        $vars = new LocateConstructorVariables();
        $this->traverse($code, $def, $vars);

        $definition = $def->getClassDefinition();
        $this->fillConstructorParams($definition);
        $this->fillConstructorVars($vars, $definition);

        //will be omitted when deps are real Dependency objects
        $definition->dependencies = $dependencies;

        return $this->resolveConflicts($definition);
    }

    private function traverse(string $code, NodeVisitor ...$visitors)
    {
        $tr = new NodeTraverser();

        foreach ($visitors as $visitor) {
            $tr->addVisitor($visitor);
        }

        $tr->traverse($this->parser->parse($code));
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
     * @param LocateConstructorVariables $vars
     * @param ClassDefinition            $definition
     */
    private function fillConstructorVars(LocateConstructorVariables $vars, ClassDefinition $definition)
    {
        $vars = $vars->getVars();
        foreach ($vars as $k => $var) {
            if (isset($definition->constructorParams[$var])) {
                unset($vars[$k]);
            }
        }

        $definition->constructorVars = $vars;
    }

    private function resolveConflicts(ClassDefinition $definition): ClassDefinition
    {
        $this->namesResolver->resolve($definition);
        $this->namespacesResolver->resolve($definition);

        return $definition;
    }
}