<?php

namespace SparkQuery\Query\Basic;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Query\Manipulation\Where;
use SparkQuery\Query\Manipulation\LimitOffset;
use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Builder\InsertBuilder;

class Insert extends BaseQuery
{

    /**
     * Constructor. Set builder type to insert
     */
    public function __construct($builder = null, string $table = '', array $options = [], $statement = null)
    {
        $this->builder = $builder instanceof InsertBuilder ? $builder : new InsertBuilder;
        $this->builder->builderType(BaseBuilder::INSERT);
        $this->table = $table;
        $this->options = $options;
        $this->statement = $statement;
    }

    /**
     * INSERT INTO query
     * @param string $table
     * @return this
     */
    public function insert($table)
    {
        if ($table) {
            $this->table($table);
        } else {
            throw new \Exception('Table name is not defined');
        }
        return $this;
    }

    /**
     * INSERT INTO SELECT query
     * @param string $table
     * @return this
     */
    public function insertCopy($table)
    {
        $this->builder->builderType(BaseBuilder::INSERT_COPY);
        return $this->insert($table);
    }

    /**
     * Add a value object to list of Column in builder object
     * @param array values
     * @return this
     */
    public function values(array $values)
    {
        $valueObject = $this->createValue($values);
        $this->builder->addValue($valueObject);
        return $this;
    }

    /**
     * Add multiple value objects to list of Column in builder object
     * @param array multiValues
     * @return this
     */
    public function multiValues(array $multiValues)
    {
        foreach ($multiValues as $values) {
            $valueObject = $this->createValue($values);
            $this->builder->addValue($valueObject);
        }
        return $this;
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

}
