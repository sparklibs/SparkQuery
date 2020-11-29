<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Query\Manipulation\Where;
use SparkQuery\Query\Manipulation\Having;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Expression;

class BaseClause extends BaseQuery
{

    /**
     * Current nested conjunctive
     * @var $nestedConjunctive
     */
    protected $nestedConjunctive = Clause::CONJUNCTIVE_NONE;

    /**
     * Creating Clause object
     * @param Column $column
     * @param mixed $operator
     * @param mixed $values
     * @param int $conjunctive
     * @param int $nestedConjunctive
     * @return Clause
     */
    protected function createClause($column, $operator, $values, int $conjunctive = -1, int $nestedConjunctive = -1)
    {
        $columnObject = $column instanceof Expression ? $column : $this->createColumn($column);
        $validOperator = $this->getOperator($operator);
        $validValues = $this->getValues($values, $validOperator);
        return new Clause($columnObject, $validOperator, $validValues, $conjunctive, $nestedConjunctive);
    }

    /**
     * Get valid operator from input operator
     * @param mixed $operator
     * @return int
     */
    protected function getOperator($operator)
    {
        if (is_int($operator)) {
            $validOperator = $operator;
        } else {
            switch ($operator) {
                case '=':
                case '==':
                    $validOperator = Clause::OPERATOR_EQUAL;
                break;
                case '!=':
                case '<>':
                    $validOperator = Clause::OPERATOR_NOT_EQUAL;
                break;
                case '>':
                    $validOperator = Clause::OPERATOR_GREATER;
                break;
                case '>=':
                    $validOperator = Clause::OPERATOR_GREATER_EQUAL;
                break;
                case '<':
                    $validOperator = Clause::OPERATOR_LESS;
                break;
                case '<=':
                    $validOperator = Clause::OPERATOR_LESS_EQUAL;
                break;
                case 'BETWEEN':
                    $validOperator = Clause::OPERATOR_BETWEEN;
                break;
                case 'NOT BETWEEN':
                    $validOperator = Clause::OPERATOR_NOT_BETWEEN;
                break;
                case 'LIKE':
                    $validOperator = Clause::OPERATOR_LIKE;
                break;
                case 'NOT LIKE':
                    $validOperator = Clause::OPERATOR_NOT_LIKE;
                break;
                case 'IN':
                    $validOperator = Clause::OPERATOR_IN;
                break;
                case 'NOT IN':
                    $validOperator = Clause::OPERATOR_NOT_IN;
                break;
                case 'NULL':
                case 'IS NULL':
                    $validOperator = Clause::OPERATOR_NULL;
                break;
                case 'NOT NULL':
                case 'IS NOT NULL':
                    $validOperator = Clause::OPERATOR_NOT_NULL;
                break;
                default:
                    $validOperator = Clause::OPERATOR_DEFAULT;
            }
        }
        return $validOperator;
    }

    /**
     * Checking input values
     * @param mixed $values
     * @param int $operator
     * @return mixed
     */
    protected function getValues($values, $operator)
    {
        switch ($operator) {
            case Clause::OPERATOR_BETWEEN:
            case Clause::OPERATOR_NOT_BETWEEN:
                $valid = (is_array($values) && count($values) >= 2) ? true : false;
                break;
            case Clause::OPERATOR_IN:
            case Clause::OPERATOR_NOT_IN:
                $valid = is_array($values) ? true : false;
            default:
                $valid = true;
        }
        if ($valid) {
            return $values;
        } else {
            throw new \Exception('Invalid input values for Where or Having clause');
        }
    }

    /**
     * Add Clause object to clause property
     * @param mixed $column
     * @param mixed $operator
     * @param mixed $values
     * @param int $conjunctive
     * @return this
     */
    protected function addClause($column, $operator, $values, $conjunctive)
    {
        $clause = $this->createClause($column, $operator, $values, $conjunctive, $this->nestedConjunctive);
        $this->nestedConjunctive = Clause::CONJUNCTIVE_NONE;
        if ($this instanceof Where) {
            $this->builder->addWhere($clause);
        } elseif ($this instanceof Having) {
            $this->builder->addHaving($clause);
        } else {
            throw new \Exception('Manipulation class is not registered as extended BaseClause class');
        }
        return $this;
    }

    private function lastClause()
    {
        if ($this instanceof Having) {
            return $this->builder->lastHaving();
        } else {
            return $this->builder->lastWhere();
        }
    }

    private function countClause()
    {
        if ($this instanceof Having) {
            return $this->builder->countHaving();
        } else {
            return $this->builder->countWhere();
        }
    }

    protected function beginNestedClause()
    {
        if ($this->nestedConjunctive <= Clause::CONJUNCTIVE_BEGIN) {
            $this->nestedConjunctive--;
        } else {
            $this->nestedConjunctive = Clause::CONJUNCTIVE_BEGIN;
        }
    }

    protected function endNestedClause()
    {
        $lastNested = $this->lastClause()->nestedConjunctive();
        if ($lastNested >= Clause::CONJUNCTIVE_END) {
            $this->lastClause()->editNestedConjunctive(++$lastNested);
        } elseif ($lastNested == Clause::CONJUNCTIVE_NONE) {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_END);
        } elseif ($lastNested == Clause::CONJUNCTIVE_AND_BEGIN) {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_AND);
        } elseif ($lastNested == Clause::CONJUNCTIVE_OR_BEGIN) {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_OR);
        } elseif ($lastNested == Clause::CONJUNCTIVE_NOT_AND_BEGIN) {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_NOT_AND);
        } elseif ($lastNested == Clause::CONJUNCTIVE_NOT_OR_BEGIN) {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_NOT_OR);
        } else {
            $this->lastClause()->editNestedConjunctive(Clause::CONJUNCTIVE_NONE);
        }
    }

    protected function getConjunctive(int $conjunctive): int
    {
        if ($this->countClause() && $this->nestedConjunctive >= Clause::CONJUNCTIVE_NONE) {
            return $conjunctive;
        } else {
            return Clause::CONJUNCTIVE_NONE;
        }
    }

    public function equal($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_EQUAL, $values, $conjunctive);
    }

    public function notEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_NOT_EQUAL, $values, $conjunctive);
    }

    public function orEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_EQUAL, $values, $conjunctive);
    }

    public function notOrEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_NOT_EQUAL, $values, $conjunctive);
    }

    public function greater($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_GREATER, $values, $conjunctive);
    }

    public function orGreater($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_GREATER, $values, $conjunctive);
    }

    public function greaterEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_GREATER_EQUAL, $values, $conjunctive);
    }

    public function orGreaterEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_GREATER_EQUAL, $values, $conjunctive);
    }

    public function less($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_LESS, $values, $conjunctive);
    }

    public function orLess($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_LESS, $values, $conjunctive);
    }

    public function lessEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_LESS_EQUAL, $values, $conjunctive);
    }

    public function orlessEqual($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_LESS_EQUAL, $values, $conjunctive);
    }

    public function between($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_BETWEEN, $values, $conjunctive);
    }

    public function notBetween($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_NOT_BETWEEN, $values, $conjunctive);
    }

    public function orBetween($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_BETWEEN, $values, $conjunctive);
    }

    public function notOrBetween($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_NOT_BETWEEN, $values, $conjunctive);
    }

    public function in($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_IN, $values, $conjunctive);
    }

    public function notIn($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_NOT_IN, $values, $conjunctive);
    }

    public function orIn($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_IN, $values, $conjunctive);
    }

    public function notOrIn($column, $values)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_NOT_IN, $values, $conjunctive);
    }

    public function isNull($column)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_NULL, null, $conjunctive);
    }

    public function isNotNull($column)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_AND);
        return $this->addClause($column, Clause::OPERATOR_NOT_NULL, null, $conjunctive);
    }

    public function orIsNull($column)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_NULL, null, $conjunctive);
    }

    public function orIsNotNull($column)
    {
        $conjunctive = $this->getConjunctive(Clause::CONJUNCTIVE_OR);
        return $this->addClause($column, Clause::OPERATOR_NOT_NULL, null, $conjunctive);
    }

}