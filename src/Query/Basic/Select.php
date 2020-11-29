<?php

namespace SparkQuery\Query\Basic;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Query\Manipulation\Where;
use SparkQuery\Query\Manipulation\GroupBy;
use SparkQuery\Query\Manipulation\Having;
use SparkQuery\Query\Manipulation\OrderBy;
use SparkQuery\Query\Manipulation\LimitOffset;
use SparkQuery\Query\Manipulation\JoinTable;
use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Builder\SelectBuilder;

class Select extends BaseQuery
{

    /**
     * Constructor. Set builder type to select
     */
    public function __construct($builder = null, $table = '', array $options = [], $statement = null)
    {
        $this->builder = $builder instanceof SelectBuilder ? $builder : new SelectBuilder;
        $this->builder->builderType(BaseBuilder::SELECT);
        $this->table = $table;
        $this->options = $options;
        $this->statement = $statement;
    }

    /**
     * SELECT query
     * @param mixed $table
     * @return this
     */
    public function select($table)
    {
        if ($table) {
            $this->table($table);
        } else {
            throw new \Exception('Table name is not defined');
        }
        return $this;
    }

    /**
     * SELECT DISTINC query
     * @param mixed $table
     * @return this
     */
    public function selectDistinct($table)
    {
        $this->builder->builderType(BaseBuilder::SELECT_DISTINCT);
        return $this->select($table);
    }

    /**
     * Add a column object to list of Column in builder object
     * @param mixed column
     * @return this
     */
    public function column($column)
    {
        $columnObject = $this->createColumn($column);
        $this->builder->addColumn($columnObject);
        return $this;
    }

    /**
     * Add multiple column objects to list of Column in builder object
     * @param array columns
     * @return this
     */
    public function columns(array $columns)
    {
        foreach ($columns as $alias => $column) {
            $columnObject = $this->createColumn([$alias => $column]);
            $this->builder->addColumn($columnObject);
        }
        return $this;
    }

    /**
     * Add a expression object to list of Column in builder object
     * @param array column
     * @return this
     */
    public function columnExpression(string $expression, string $alias = '', array $params = [])
    {
        $expressionObject = $this->createExpression($expression, $alias, $params);
        $this->builder->addColumn($expressionObject);
        return $this;
    }

    /**
     * starting WHERE query manipulation 
     */
    private function whereManipulation()
    {
        return new Where($this->builder, $this->table, $this->options, $this->statement);
    }

    /** WHERE query manipulation method */
    public function beginWhere()
    {
        return $this->whereManipulation()->beginWhere();
    }

    /** WHERE query manipulation method */
    public function where($column, string $operator, $values = null)
    {
        return $this->whereManipulation()->where($column, $operator, $values);
    }

    /** WHERE query manipulation method */
    public function orWhere($column, string $operator, $values = null)
    {
        return $this->whereManipulation()->orWhere($column, $operator, $values);
    }

    /** WHERE query manipulation method */
    public function whereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        return $this->whereManipulation()->whereExpression($expression, $operator, $values, $params);
    }

    /** WHERE query manipulation method */
    public function orWhereExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        return $this->whereManipulation()->whereExpression($expression, $operator, $values, $params);
    }

    /**
     * starting HAVING query manipulation 
     */
    private function havingManipulation()
    {
        return new Having($this->builder, $this->table, $this->options, $this->statement);
    }

    /** HAVING query manipulation method */
    public function beginHaving()
    {
        return $this->havingManipulation()->beginHaving();
    }

    /** HAVING query manipulation method */
    public function having($column, string $operator, $values = null)
    {
        return $this->havingManipulation()->having($column, $operator, $values);
    }

    /** HAVING query manipulation method */
    public function orHaving($column, string $operator, $values = null)
    {
        return $this->havingManipulation()->orHaving($column, $operator, $values);
    }

    /** WHERE query manipulation method */
    public function havingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        return $this->havingManipulation()->havingExpression($expression, $operator, $values, $params);
    }

    /** WHERE query manipulation method */
    public function orHavingExpression(string $expression, string $operator, $values = null, array $params = [])
    {
        return $this->havingManipulation()->havingExpression($expression, $operator, $values, $params);
    }

    /**
     * Starting GROUP BY query manipulation
     */
    private function groupByManipulation()
    {
        return new GroupBy($this->builder, $this->table, $this->options, $this->statement);
    }

    /** GROUP BY query manipulation method */
    public function groupBy($column)
    {
        return $this->groupByManipulation()->groupBy($column);
    }

    /** GROUP BY query manipulation method */
    public function groupsBy(array $columns)
    {
        return $this->groupByManipulation()->groupsBy($columns);
    }

    /**
     * Starting ORDER BY query manipulation
     */
    private function orderByManipulation()
    {
        return new OrderBy($this->builder, $this->table, $this->options, $this->statement);
    }

    /** ORDER BY query manipulation method */
    public function orderBy($column, $orderType)
    {
        return $this->orderByManipulation()->orderBy($column, $orderType);
    }

    /** ORDER BY query manipulation method */
    public function ordersBy(array $orders)
    {
        return $this->orderByManipulation()->ordersBy($orders);
    }

    /** ORDER BY query manipulation method */
    public function orderAsc($column)
    {
        return $this->orderByManipulation()->orderAsc($column);
    }

    /** ORDER BY query manipulation method */
    public function orderDesc($column)
    {
        return $this->orderByManipulation()->orderDesc($column);
    }

    /**
     * Starting LIMIT and OFFSET query manipulation
     */
    private function limitOffsetManipulation()
    {
        return new LimitOffset($this->builder, $this->table, $this->options, $this->statement);
    }

    /** LIMIT query manipulation method */
    public function limit(int $limit, int $offset = null)
    {
        return $this->limitOffsetManipulation()->limit($limit, $offset);
    }

    /** OFFSET query manipulation method */
    public function offset($offset)
    {
        return $this->limitOffsetManipulation()->offset($offset);
    }

    /**
     * Starting JOIN query manipulation
     */
    private function joinTableManipulation()
    {
        return new JoinTable($this->builder, $this->table, $this->options, $this->statement);
    }

    /** JOIN query manipulation method */
    public function join($joinTable, $jointType)
    {
        return $this->joinTableManipulation()->join($joinTable, $jointType);
    }

    /** INNER JOIN query manipulation method */
    public function innerJoin($joinTable)
    {
        return $this->joinTableManipulation()->innerJoin($joinTable);
    }

    /** LEFT JOIN query manipulation method */
    public function leftJoin($joinTable)
    {
        return $this->joinTableManipulation()->leftJoin($joinTable);
    }

    /** RIGHT JOIN query manipulation method */
    public function rightJoin($joinTable)
    {
        return $this->joinTableManipulation()->rightJoin($joinTable);
    }

    /** OUTER JOIN query manipulation method */
    public function outerJoin($joinTable)
    {
        return $this->joinTableManipulation()->outerJoin($joinTable);
    }

}