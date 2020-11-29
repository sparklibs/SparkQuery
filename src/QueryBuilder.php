<?php

namespace SparkQuery;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Query\Basic\Select;
use SparkQuery\Query\Basic\Insert;
use SparkQuery\Query\Basic\Update;
use SparkQuery\Query\Basic\Delete;
use SparkQuery\Builder\SelectBuilder;
use SparkQuery\Builder\InsertBuilder;
use SparkQuery\Builder\UpdateBuilder;
use SparkQuery\Builder\DeleteBuilder;

class QueryBuilder
{

    /**
     * Default query translator and binding mode option
     * @var $translator
     */
    private $options;

    /**
     * Callable of statement function
     */
    public $statement;

    /**
     * Default table name
     * @var $table
     */
    private $table = '';

    /**
     * Constructor. Set options and create query translator object
     */
    public function __construct(int $translator = QueryTranslator::TRANSLATOR_MYSQL, int $bindingOption = QueryTranslator::PARAM_NUM)
    {
        // $this->options = QueryTranslator::getBindingOption($bindingOption);
        $this->options = [$translator, $bindingOption];
        $this->statement = null;
    }

    /**
     * Set statement function to connect this QueryBuilder library to a Database driver
     * @param Callable $statement
     */
    public function setStatement(Callable $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Set table name
     * @param string $table
     * @return this
     */
    public function setTable(string $table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * begin SELECT query builder
     * @param mixed $table
     * @return this
     */
    public function select($table = null)
    {
        $table !== null ?: $table = $this->table;
        $selectQuery = new Select(null, $table, $this->options, $this->statement);
        return $selectQuery->select($table);
    }

    /**
     * begin SELECT DISTINCT query builder
     * @param mixed $table
     * @return this
     */
    public function selectDistinct($table = null)
    {
        $table !== null ?: $table = $this->table;
        $selectQuery = new Select(null, $table, $this->options, $this->statement);
        return $selectQuery->selectDistinct($table);
    }

    /**
     * begin INSERT INTO query builder
     * @param mixed $table
     * @return this
     */
    public function insert($table = null)
    {
        $table !== null ?: $table = $this->table;
        $insertQuery = new Insert(null, $table, $this->options, $this->statement);
        return $insertQuery->insert($table);
    }

    /**
     * begin INSERT INTO SELECT query builder
     * @param mixed $table
     * @return this
     */
    public function insertCopy($table = null)
    {
        $table !== null ?: $table = $this->table;
        $insertQuery = new Insert(null, $table, $this->options, $this->statement);
        return $insertQuery->insertCopy($table);
    }

    /**
     * begin UPDATE query builder
     * @param mixed $table
     * @return this
     */
    public function update($table = null)
    {
        $table !== null ?: $table = $this->table;
        $updateQuery = new Update(null, $table, $this->options, $this->statement);
        return $updateQuery->update($table);
    }

    /**
     * begin DELETE query builder
     * @param mixed $table
     * @return this
     */
    public function delete($table = null)
    {
        $table !== null ?: $table = $this->table;
        $deleteQuery = new Delete(null, $table, $this->options, $this->statement);
        return $deleteQuery->delete($table);
    }

}
