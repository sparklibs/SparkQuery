<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Clause;

interface Iwhere
{

    /**
     * Get array of where Clause object
     * @return array of Clause object
     */
    public function getWhere(): array;

    /**
     * Get last of where Clause object
     * @return Clause object or null
     */
    public function lastWhere();

    /**
     *  Count number of where Clause object already added in builder object
     * @return int
     */
    public function countWhere(): int;

    /**
     * Add where Clause object to where property of builder object
     * @param Clause $where
     */
    public function addWhere(Clause $where);

}
