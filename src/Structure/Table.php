<?php

namespace SparkQuery\Structure;

class Table
{

    /**
     * Table name
     */
    private $name;

    /**
     * Table alias name
     */
    private $alias;

    /**
     * Constructor. Set table name and alias
     */
    public function __construct(string $name, string $alias = '')
    {
        $this->name = $name;
        $this->alias = $alias;
    }

    /** Get table name */
    public function name()
    {
        return $this->name;
    }

    /** Get table alias */
    public function alias()
    {
        return $this->alias;
    }

}
