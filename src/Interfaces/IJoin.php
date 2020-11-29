<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Join;

interface IJoin
{

    /**
     * Get array of Join table object
     * @return array of Clause object
     */
    public function getJoin(): array;

    /**
     * Get last of Join table or Table object
     * @return Join object, Table object, or null
     */
    public function lastJoin();

    /**
     *  Count number of Join table object already added in builder object
     * @return int
     */
    public function countJoin(): int;

    /**
     * Add Join table object to where property of builder object
     * @param Join $join
     */
    public function addJoin(Join $join);

}
