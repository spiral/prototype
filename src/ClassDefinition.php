<?php
declare(strict_types=1);

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

    static public function create(string $class): ClassDefinition
    {
        $self = new self();
        $self->class = $class;

        return $self;
    }

    static public function createWithNamespace(string $class, string $namespace): ClassDefinition
    {
        $self = new self();
        $self->class = $class;
        $self->namespace = $namespace;

        return $self;
    }

    public function addImportUsage(string $name, ?string $alias)
    {
        $this->addStmt(ClassDefinition\ClassStmt::create($name, $alias));
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

    private function __construct()
    {
    }
}