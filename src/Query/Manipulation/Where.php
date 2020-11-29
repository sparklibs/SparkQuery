<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\Manipulation\BaseClause;
use SparkQuery\Interfaces\IWhere;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Clause;

class Where extends BaseClause
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        if ($builderObject instanceof IWhere) {
            $this->builder = $builderObject;
            $this->table = $table;
            $this->options = $options;
            $this->statement = $statement;
        } else {
            throw new \Exception('Builder object not support Where manipulation');
        }
    }

    /**
     * Call function for non-exist method calling.
     * Used for invoking next manipulation method in different class
     */
    public function __call($function, $arguments)
    {
        return $this->callQuery($function, $arguments);
    }

    public function beginWhere()
    {
        $this->beginNestedClause();
        return $this;
    }

    public function beginAndWhere()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_AND_BEGIN;
        return $this;
    }

    public function beginOrWhere()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_OR_BEGIN;
        return $this;
    }

    public function beginNotAndWhere()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NOT_AND_BEGIN;
        return $this;
    }

    public function beginNotOrWhere()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NOT_OR_BEGIN;
        return $this;
    }

    public function endWhere()
    {
        $this->endNestedClause();
        return $this;
    }

    /**
     * Add Clause object to where property of Builder object
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $values
     * @param int $conjunctive
     */
    public function addWhere($column, $operator, $values, int $conjunctive)
    {
        $clause = $this->createClause($column, $operator, $values, $conjunctive, $this->nestedConjunctive);
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NONE;
        $this->builder->addWhere($clause);
        return $this;
    }

    public function where($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addWhere($column, $operator, $values, $conjunctive);
    }

    public function andWhere($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addWhere($column, $operator, $values, $conjunctive);
    }

    public function orWhere($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addWhere($column, $operator, $values, $conjunctive);
    }

    public function notAndWhere($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_AND);
        return $this->addWhere($column, $operator, $values, $conjunctive);
    }

    public function notOrWhere($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_OR);
        return $this->addWhere($column, $operator, $values, $conjunctive);
    }

    public function whereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addWhere($expressionObject, $operator, $values, $conjunctive);
    }

    public function orWhereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addWhere($expressionObject, $operator, $values, $conjunctive);
    }

    public function notAndWhereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_AND);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addWhere($expressionObject, $operator, $values, $conjunctive);
    }

    public function notOrWhereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_OR);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addWhere($expressionObject, $operator, $values, $conjunctive);
    }

}
