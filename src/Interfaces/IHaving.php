<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Clause;

interface IHaving
{

    /**
     * Get array of having Clause object
     * @return array of Clause object
     */
    public function getHaving(): array;

    /**
     * Get last of having Clause object
     * @return Clause object or null
     */
    public function lastHaving();

    /**
     *  Count number of having Clause object already added in builder object
     * @return int
     */
    public function countHaving(): int;

    /**
     * Add having Clause object to having property of builder object
     * @param Clause $having
     */
    public function addHaving(Clause $having);

}
