<?php

namespace SparkQuery\Structure;

class Column
{

    /**
     * Table name or table alias of column
     */
    private $table;

    /**
     * Column name
     */
    private $name;

    /**
     * Alias name of column
     */
    private $alias;

    /**
     * Aggregate function applied to column
     */
    private $function;

    /**
     * Constructor. Clear all properties
     */
    public function __construct(string $table, string $name, string $alias = '', string $function = '')
    {
        $this->table = $table;
        $this->name = $name;
        $this->alias = $alias;
        $this->function = $function;
    }

    /** Get table name or alias of column */
    public function table()
    {
        return $this->table;
    }

    /** Get name of column */
    public function name()
    {
        return $this->name;
    }

    /** Get table name of column */
    public function alias()
    {
        return $this->alias;
    }

    /** Get table name of column */
    public function function()
    {
        return $this->function;
    }

}
