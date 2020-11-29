<?php

namespace SparkQuery\Structure;

use SparkQuery\Structure\Table;
use SparkQuery\Structure\Column;

class Join
{

    /**
     * Type of join of the table
     */
    private $joinType;

    /** Join type options */
    public const NO_JOIN = 0;
    /** Join type options */
    public const INNER_JOIN = 1;
    /** Join type options */
    public const LEFT_JOIN = 2;
    /** Join type options */
    public const RIGHT_JOIN = 3;
    /** Join type options */
    public const OUTER_JOIN = 4;

    /**
     * Base table name or alias for join query
     */
    private $baseTable;

    /**
     * Join table name
     */
    private $joinTable;

    /**
     * Join table alias name
     */
    private $joinAlias;

    /**
     * Columns of base table
     */
    private $baseColumns;

    /**
     * Columns of the table to be joined with ON keyword
     */
    private $joinColumns;

    /**
     * Columns list of table to be joined with USING keyword
     */
    private $usingColumns;

    /**
     * Constructor. Set join type, base table, and join table
     */
    public function __construct(int $joinType, string $baseTable, string $joinTable, string $joinAlias = '')
    {
        $this->joinType = $joinType;
        $this->baseTable = $baseTable;
        $this->joinTable = $joinTable;
        $this->joinAlias = $joinAlias;
        $this->baseColumns = [];
        $this->joinColumns = [];
        $this->usingColumns = [];
    }

    /**
     * Add a join column with ON keyword or USING keyword
     */
    public function addJoinColumn(Column $column1, $column2)
    {
        if ($column2 instanceof Column) {
            $this->baseColumns[] = $column1;
            $this->joinColumns[] = $column2;
        } else {
            $this->usingColumns[] = $column1;
        }
    }

    /** Get join type */
    public function joinType(): int
    {
        return $this->joinType;
    }

    /** Get base table */
    public function baseTable(): string
    {
        return $this->baseTable;
    }

    /** Get join table name */
    public function joinTable(): string
    {
        return $this->joinTable;
    }

    /** Get join table alias name */
    public function joinAlias(): string
    {
        return $this->joinAlias;
    }

    /** Get base column */
    public function baseColumns(): array
    {
        return $this->baseColumns;
    }

    /** Get join column */
    public function joinColumns(): array
    {
        return $this->joinColumns;
    }

    /** Get using column */
    public function usingColumns(): array
    {
        return $this->usingColumns;
    }

}
