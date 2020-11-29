<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Structure\Join;

class JoinTable extends BaseQuery
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        $this->builder = $builderObject;
        $this->table = $table;
        $this->options = $options;
        $this->statement = $statement;
    }

    /**
     * Call function for non-exist method calling.
     * Used for invoking next manipulation method in different class
     */
    public function __call($function, $arguments)
    {
        return $this->callQuery($function, $arguments);
    }

    /**
     * Create join table object from input table
     * @param mixed $joinTable
     * @param int $joinType
     * @return Table
     */
    private function createJoinTable(int $joinType, $joinTable)
    {
        if (is_array($joinTable)) {
            $joinAlias = strval(array_keys($joinTable)[0]);
            $tableObject = new Join($joinType, $this->table, strval(array_values($joinTable)[0]), $joinAlias);
        } else {
            $joinTable = strval($joinTable);
            $tableObject = new Join($joinType, $this->table, $joinTable);
        }
        return $tableObject;
    }

    /**
     * Edit join table object base and join columns Table object property
     * @param mixed $column1
     * @param mixed $column2
     * @return
     */
    private function addJoinColumn($column1, $column2 = null)
    {
        $joinObject = $this->builder->lastJoin();
        if ($joinObject instanceof Join) {
            $this->table = $joinObject->baseTable();
            $columnObject1 = $this->createColumn($column1);
            $this->table = $joinObject->joinAlias()
                ? $joinObject->joinAlias()
                : $joinObject->joinTable()
            ;
            $columnObject2 = $column2 ? $this->createColumn($column2) : null;
            $joinObject->addJoinColumn($columnObject1, $columnObject2);
        }
    }

    /**
     * Add a JOIN table to Table list
     * @param string $joinTable
     * @param mixed $jointType
     * @param this
     */
    public function join($joinTable, $joinType)
    {
        if (is_int($joinType) && $joinType > 0 && $joinType <= 4) {
            $validType = $joinType;
        } else {
            switch ($joinType) {
                case 'INNER':
                case 'INNER JOIN':
                    $validType = Table::INNER_JOIN;
                break;
                case 'LEFT':
                case 'LEFT JOIN':
                    $validType = Table::LEFT_JOIN;
                break;
                case 'RIGHT':
                case 'RIGHT JOIN':
                    $validType = Table::RIGHT_JOIN;
                break;
                case 'OUTER':
                case 'OUTER JOIN':
                    $validType = Table::OUTER_JOIN;
                break;
                default:
                    $validType = Table::BASE_TABLE;
            }
        }
        $joinObject = $this->createJoinTable($validType, $joinTable);
        $this->builder->addJoin($joinObject);
        return $this;
    }

    /** INNER JOIN query manipulation method */
    public function innerJoin($joinTable)
    {
        return $this->join($joinTable, Join::INNER_JOIN);
    }

    /** LEFT JOIN query manipulation method */
    public function leftJoin($joinTable)
    {
        return $this->join($joinTable, Join::LEFT_JOIN);
    }

    /** RIGHT JOIN query manipulation method */
    public function rightJoin(string $joinTable)
    {
        return $this->join($joinTable, Join::RIGHT_JOIN);
    }

    /** OUTER JOIN query manipulation method */
    public function outerJoin(string $joinTable)
    {
        return $this->join($joinTable, Join::OUTER_JOIN);
    }

    /**
     * Edit a JOIN table from Join list to build ON query
     * @param mixed $column1
     * @param mixed $column2
     * @param this
     */
    public function on($column1, $column2)
    {
        $this->addJoinColumn($column1, $column2);
        return $this;
    }

    /**
     * Edit a JOIN table from Join list to build USING query
     * @param mixed $column
     * @param this
     */
    public function using($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        foreach ($columns as $column) {
            $this->addJoinColumn($column);
        }
        return $this;
    }


}
