<?php

namespace SparkQuery\Builder;

use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Limit;
use SparkQuery\Interfaces\IWhere;
use SparkQuery\Interfaces\ILimit;

class InsertBuilder extends BaseBuilder implements ILimit
{

    /**
     * Limit structure object created by Limit manipulating class
     */
    private $limit = null;

    /**
     * Interface ILimit required method
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Interface ILimit required method
     */
    public function hasLimit(): bool
    {
        return ($this->limit instanceof Limit);
    }

    /**
     * Interface ILimit required method
     */
    public function setLimit(Limit $limit)
    {
        if (empty($this->limit)) {
            $this->limit = $limit;
        }
    }

}
