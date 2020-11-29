<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Interfaces\ILimit;
use SparkQuery\Structure\Limit;

class LimitOffset extends BaseQuery
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        if ($builderObject instanceof ILimit) {
            $this->builder = $builderObject;
            $this->table = $table;
            $this->options = $options;
            $this->statement = $statement;
        } else {
            throw new \Exception('Builder object not support LimitOffset manipulation');
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
     * LIMIT query manipulation
     * @param int $limit
     * @param mixed $offset
     * @return this
     */
    public function limit(int $limit, $offset)
    {
        is_int($offset) ?: $offset = Limit::NOT_SET;
        $limitObject = new Limit($limit, $offset);
        $this->builder->setLimit($limitObject);
        return $this;
    }

    /**
     * OFFSET query manipulation
     * @param int $offset
     * @return this
     */
    public function offset(int $offset)
    {
        $limitObject = new Limit(Limit::NOT_SET, $offset);
        $this->builder->setLimit($limitObject);
        return $this;
    }

}