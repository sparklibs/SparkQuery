<?php

namespace SparkQuery\Query;

use SparkQuery\QueryTranslator;
use SparkQuery\Query\Basic\Select;
use SparkQuery\Query\Basic\Insert;
use SparkQuery\Query\Basic\Update;
use SparkQuery\Query\Basic\Delete;
use SparkQuery\Structure\Table;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Value;
use SparkQuery\Structure\Expression;
use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Builder\SelectBuilder;
use SparkQuery\Builder\InsertBuilder;
use SparkQuery\Builder\UpdateBuilder;
use SparkQuery\Builder\DeleteBuilder;

class BaseQuery
{

    /**
     * Query object. Contain result of translated builder object
     */
    private $query;

    /**
     * Builder object to manipulate in basic query and manipulation query class
     * @var $builder
     */
    protected $builder;

    /**
     * Default table name or alias
     * @var $table
     */
    protected $table = '';

    /**
     * Translator and binding options from QueryBuilder class
     */
    protected $options;

    /**
     * Statement callable from QueryBuilder class
     */
    protected $statement;

    /**
     * Return current state of query object
     * @return QueryObject
     */
    public function queryObject()
    {
        return $this->query;
    }

    /** 
     * Set default table name
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /** 
     * Get builder object
     * @return
     */
    public function getBuilder(): BaseBuilder
    {
        return $this->builder;
    }

    /**
     * Call a method from new basic query class
     * @param mixed $method
     * @param mixed $arguments
     * @return
     */
    protected function callQuery($method, $arguments)
    {
        switch (true) {
            case $this->builder instanceof SelectBuilder:
                $queryClass = new Select($this->builder, $this->table, $this->options, $this->statement);
                break;
            case $this->builder instanceof InsertBuilder:
                $queryClass = new Insert($this->builder, $this->table, $this->options, $this->statement);
                break;
            case $this->builder instanceof UpdateBuilder:
                $queryClass = new Update($this->builder, $this->table, $this->options, $this->statement);
                break;
            case $this->builder instanceof DeleteBuilder:
                $queryClass = new Delete($this->builder, $this->table, $this->options, $this->statement);
                break;
            default:
                throw new \Exception('Unregistered query class method is tried to call');
        }
        return call_user_func_array([$queryClass, $method], $arguments);
    }

    /**
     * Add a table object to list of Table in builder object
     * @param mixed $table
     * @return this
     */
    public function table($table)
    {
        $this->builder->setTable($this->createTable($table));
        return $this;
    }

    /**
     * Create table object from string input table
     * @param mixed $table
     * @return Table
     */
    protected function createTable($table)
    {
        if (is_array($table)) {
            $alias = strval(array_keys($table)[0]);
            $tableObject = new Table(strval(array_values($table)[0]), $alias);
            $this->table = $alias;
        } else {
            $name = strval($table);
            $tableObject = new Table($name);
            $this->table = $name;
        }
        return $tableObject;
    }

    /**
     * Create column object from any input column
     * @param mixed $column
     * @return Column
     */
    protected function createColumn($column)
    {
        $table = '';
        $name = '';
        $function = '';
        $alias = '';
        if (is_string($column)) {
            list($table, $name, $function) = $this->parseColumnString($column);
        } elseif (is_array($column)) {
            list($table, $name, $function, $alias) = $this->parseColumnArray($column);
        }
        $columnObject = new Column($table, $name, $alias, $function);
        return $columnObject;
    }

    /**
     * Parsing string input column to table, column name, and aggregate function
     * @param string $column
     * @return array
     */
    private function parseColumnString(string $column)
    {
        $function = '';
        if (substr($column, -1, 1) == ')' && false !== $pos = strpos($column, '(')) {
            $function = substr($column, 0, $pos);
            $column = substr($column, $pos+1, strlen($column)-$pos-2);
        }
        $exploded = explode('.', $column, 2);
        if (count($exploded) == 2) {
            $table = trim($exploded[0], "\"\`\'\r\n ");
            $name = trim($exploded[1], "\"\`\'\r\n ");
        } else {
            $table = $this->table;
            $name = trim($column, "\"\`\'\r\n ");
        }
        return [$table, $name, $function];
    }

    /**
     * Parsing array input column to table, column name, aggregate function, and alias name
     * @param array $column
     * @return array
     */
    private function parseColumnArray(array $column)
    {
        $alias = '';
        $keys = array_keys($column);
        if (count($keys) == 1) {
            is_int($keys[0]) ?: $alias = $keys[0];
            $column = $column[$keys[0]];
        }
        $table = isset($column['table']) ? $column['table'] : $this->table;
        $name = isset($column['column']) ? $column['column'] : '';
        $function = isset($column['function']) ? $column['function'] : '';
        if (is_string($column)) {
            list($table, $name, $function) = $this->parseColumnString($column);
        }
        return [$table, $name, $function, $alias];
    }

    /**
     * Create Value object from any input value
     * @param array $inputValue
     * @return Value
     */
    protected function createValue(array $inputValue)
    {
        $columns = [];
        $values = [];
        $len = count($inputValue);
        $lenRec = count($inputValue, 1);
        if ($len == $lenRec) {
            $columns = array_keys($inputValue);
            $values = array_values($inputValue);
        } elseif ($len + $len == $lenRec) {
            if ($len == 2) {
                list($columns, $values) = $inputValue;
            } else {
                list($columns, $values) = $this->parseValuePair($inputValue);
            }
        }
        $valueObject = new Value($this->table, $columns, $values);
        return $valueObject;
    }

    /**
     * Parsing horizontal array and return vertical array
     * @param array $pairs
     * @return array
     */
    private function parseValuePair(array $pairs)
    {
        $columns = [];
        $values = [];
        foreach ($pairs as $pair) {
            if (isset($pair[0]) && isset($pair[1])) {
                $columns[] = $pair[0];
                $values[] = $pair[1];
            } else {
                return [[], []];
            }
        }
        return [$columns, $values];
    }

    /**
     * Create Expression object used in column list, where or having clause column, or group by column
     * @param array $expression
     * @param string $alias
     * @param array $params
     * @return Expression
     */
    protected function createExpression(string $expression, string $alias = '', array $params = [])
    {
        $exploded = explode('?', $expression);
        $expressionObject = new Expression($exploded, $alias, $params);
        return $expressionObject;
    }

    /**
     * Translate builder object to query object
     */
    private function translate(int $translator)
    {
        if (!($this->query instanceof QueryObject)) {
            $translator != 0 ?: $translator = $this->options[0];
            $this->query = new QueryObject;
            QueryTranslator::translateBuilder($this->query, $this->builder, $translator);
        }
    }

    /**
     * Get query string of current builder object
     */
    public function query(int $translator = 0, int $bindingOption = 0)
    {
        $this->translate($translator);
        $bindingOption != 0 ?: $bindingOption = $this->options[1];
        return QueryTranslator::getQuery($this->query, $bindingOption);
    }

    /**
     * Get query parameters of current builder object
     */
    public function params(int $translator = 0, int $bindingOption = 0)
    {
        $this->translate($translator);
        $bindingOption != 0 ?: $bindingOption = $this->options[1];
        return QueryTranslator::getParams($this->query, $bindingOption);
    }

    /**
     * Return statement object from defined statement callable
     */
    public function getStatement($statementOptions = null, int $translator = 0, int $bindingOption = 0)
    {
        $query = $this->query($translator, $bindingOption);
        $params = $this->params($translator, $bindingOption);
        $fetchable = ($this instanceof SelectBuilder) ? true : false;
        if (is_callable($this->statement)) {
            return call_user_func_array($this->statement, [$query, $params, $fetchable, $statementOptions]);
        } else {
            throw new \Exception('Statement callable function was not defined');
        }
    }

}
