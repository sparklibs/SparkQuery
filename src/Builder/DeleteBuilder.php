<?php

namespace SparkQuery\Builder;

use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Limit;
use SparkQuery\Interfaces\IWhere;
use SparkQuery\Interfaces\ILimit;

class DeleteBuilder extends BaseBuilder implements IWhere, ILimit
{

    /**
     * Array of Clause structure object created by Where manipulation class
     */
    private $where = [];

    /**
     * Limit structure object created by Limit manipulating class
     */
    private $limit = null;

    /**
     * Interface Iwhere required method
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * Interface Iwhere required method
     */
    public function lastWhere()
    {
        $len = count($this->where);
        if ($len > 0) {
            return $this->where[$len-1];
        } else {
            return null;
        }
    }

    /**
     * Interface Iwhere required method
     */
    public function countWhere(): int
    {
        return count($this->where);
    }

    /**
     * Interface Iwhere required method
     */
    public function addWhere(Clause $where)
    {
        $this->where[] = $where;
    }

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
