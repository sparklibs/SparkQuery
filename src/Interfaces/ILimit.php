<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Limit;

interface ILimit
{

    /**
     * Get limit object
     * @return Limit object
     */
    public function getLimit();

    /**
     * Check if builder object has Limit object
     * @return bool
     */
    public function hasLimit(): bool;

    /**
     * Set limit object
     * @param Limit $limit
     */
    public function setLimit(Limit $limit);

}
