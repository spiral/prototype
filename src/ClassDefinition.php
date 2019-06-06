<?php
/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */
declare(strict_types=1);

namespace Spiral\Prototype;

use Spiral\Prototype\ClassDefinition\ConstructorParam;

final class ClassDefinition
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

    /**
     * @param string $class
     * @return ClassDefinition
     */
    static public function create(string $class): ClassDefinition
    {
        $self = new self();
        $self->class = $class;

        return $self;
    }

    /**
     * @param string $class
     * @param string $namespace
     * @return ClassDefinition
     */
    static public function createWithNamespace(string $class, string $namespace): ClassDefinition
    {
        $self = new self();
        $self->class = $class;
        $self->namespace = $namespace;

        return $self;
    }

    /**
     * @param string      $name
     * @param string|null $alias
     */
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

    /**
     * @param \ReflectionParameter $parameter
     *
     * @throws \ReflectionException
     */
    public function addParam(\ReflectionParameter $parameter)
    {
        $this->constructorParams[$parameter->name] = ConstructorParam::createFromReflection($parameter);
    }

    /**
     * @param ClassDefinition\ClassStmt $stmt
     */
    private function addStmt(ClassDefinition\ClassStmt $stmt)
    {
        $this->stmts[(string)$stmt] = $stmt;
    }

    /**
     * ClassDefinition constructor.
     */
    private function __construct()
    {
    }
}