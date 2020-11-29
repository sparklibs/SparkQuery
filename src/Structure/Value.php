<?php

namespace SparkQuery\Structure;

class Value
{

    /**
     * Table name or table alias of values
     */
    private $table;

    /**
     * Array of column name of values
     */
    private $columns;

    /**
     * Array of values
     */
    private $values;

    /**
     * Constructor. Set columns array and values array
     */
    public function __construct(string $table, array $columns, array $values)
    {
        $this->table = $table;
        $this->columns = [];
        $this->values = [];
        $lenCol = count($columns);
        $lenVal = count($values);
        $len = $lenCol < $lenVal ? $lenCol : $lenVal;
        for ($i = 0; $i < $len; $i++) {
            $this->columns[] = $columns[$i];
            $this->values[] = $values[$i];
        }
    }

    /** Get table name of values */
    public function table()
    {
        return $this->table;
    }

    /** Get column name of values */
    public function columns()
    {
        return $this->columns;
    }

    /** Get values */
    public function values()
    {
        return $this->values;
    }

}
