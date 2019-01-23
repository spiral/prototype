<?php

namespace Spiral\Prototype;

use Spiral\Prototype\ClassDefinition\ConstructorParam;

class ClassDefinition
{
    /** @var string */
    public $namespace;

    /** @var string */
    public $class;

    /** @var ClassDefinition\ClassStmt[] */
    private $stmts = [];

    /** @var ClassDefinition\ConstructorParam[] */
    public $constructorParams = [];

    /** @var string[] */
    public $constructorVars = [];

    /** @var bool */
    public $hasConstructor = false;

    /** @var Dependency[] */
    public $dependencies = [];

    static public function createEmpty(): ClassDefinition
    {
        return new self();
    }

    static public function createFromNamespace(string $namespace): ClassDefinition
    {
        $cr = new self();
        $cr->namespace = $namespace;

        return $cr;
    }

    public function addImportUsage(string $name, ?string $alias)
    {
        $this->addStmt(ClassDefinition\ClassStmt::createFromImport($name, $alias));
    }

    public function addInstantiation(string $name)
    {
        $this->addStmt(ClassDefinition\ClassStmt::createFromInstantiation($name, $this->isImported($name)));
    }

    /**
     * @return ClassDefinition\ClassStmt[]
     */
    public function getStmts(): array
    {
        return $this->stmts;
    }

    public function addParam(\ReflectionParameter $parameter)
    {
        $this->constructorParams[$parameter->name] = ConstructorParam::createFromReflection($parameter);
    }

    private function addStmt(ClassDefinition\ClassStmt $stmt)
    {
        $this->stmts[(string)$stmt] = $stmt;
    }

    private function isImported(string $name): bool
    {
        foreach ($this->stmts as $import) {
            if ($import->name === $name) {
                return true;
            }
        }

        return false;
    }

    private function __construct()
    {
    }
}