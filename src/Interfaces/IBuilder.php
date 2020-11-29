<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Table;

interface IBuilder
{

    /**
     * Get or set builder object type
     */
    public function builderType(int $type): int;

    /**
     * Get table
     */
    public function getTable(): Table;

    /**
     * Get array of tables
     */
    public function getColumns(): array;

    /**
     * Get array of values
     */
    public function getValues(): array;

}
