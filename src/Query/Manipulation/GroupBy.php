<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Interfaces\IGroupBy;
use SparkQuery\Structure\Column;

class GroupBy extends BaseQuery
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        if ($builderObject instanceof IGroupBy) {
            $this->builder = $builderObject;
            $this->table = $table;
            $this->options = $options;
            $this->statement = $statement;
        } else {
            throw new \Exception('Builder object not support GroupBy manipulation');
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

    /**
     * GROUP BY query manipulation
     * @param mixed $column
     * @return this
     */
    public function groupBy($column)
    {
        $columnObject = $this->createColumn($column);
        $this->builder->addGroup($columnObject);
        return $this;
    }

    /**
     * GROUP BY query manipulation with multiple inputs
     * @param mixed $column
     * @return this
     */
    public function groupsBy(array $columns)
    {
        foreach ($columns as $column) {
            $this->groupBy($column);
        }
        return $this;
    }

}