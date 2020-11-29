<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\Manipulation\BaseClause;
use SparkQuery\Interfaces\IHaving;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Clause;

class Having extends BaseClause
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        if ($builderObject instanceof IHaving) {
            $this->builder = $builderObject;
            $this->table = $table;
            $this->options = $options;
            $this->statement = $statement;
        } else {
            throw new \Exception('Builder object not support Having manipulation');
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

    public function beginHaving()
    {
        $this->beginNestedClause();
        return $this;
    }

    public function beginAndHaving()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_AND_BEGIN;
        return $this;
    }

    public function beginOrHaving()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_OR_BEGIN;
        return $this;
    }

    public function beginNotAndHaving()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NOT_AND_BEGIN;
        return $this;
    }

    public function beginNotOrHaving()
    {
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NOT_OR_BEGIN;
        return $this;
    }

    public function endHaving()
    {
        $this->endNestedClause();
        return $this;
    }

    /**
     * Add Clause object to having property of Builder object
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $values
     * @param int $conjunctive
     */
    public function addHaving($column, $operator, $values, int $conjunctive)
    {
        $clause = $this->createClause($column, $operator, $values, $conjunctive, $this->nestedConjunctive);
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NONE;
        $this->builder->addHaving($clause);
        return $this;
    }

    public function having($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addHaving($column, $operator, $values, $conjunctive);
    }

    public function andHaving($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addHaving($column, $operator, $values, $conjunctive);
    }

    public function orHaving($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addHaving($column, $operator, $values, $conjunctive);
    }

    public function notAndHaving($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_AND);
        return $this->addHaving($column, $operator, $values, $conjunctive);
    }

    public function notOrHaving($column, string $operator, $values = null)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_OR);
        return $this->addHaving($column, $operator, $values, $conjunctive);
    }

    public function havingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addHaving($expressionObject, $operator, $values, $conjunctive);
    }

    public function orHavingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addHaving($expressionObject, $operator, $values, $conjunctive);
    }

    public function notAndHavingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_AND);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addHaving($expressionObject, $operator, $values, $conjunctive);
    }

    public function notOrHavingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_NOT_OR);
        $expressionObject = $this->createExpression($expression, '', $params);
        return $this->addHaving($expressionObject, $operator, $values, $conjunctive);
    }

}
