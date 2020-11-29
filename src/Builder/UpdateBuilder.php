<?php

namespace SparkQuery\Builder;

use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Structure\Join;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Limit;
use SparkQuery\Interfaces\IJoin;
use SparkQuery\Interfaces\IWhere;
use SparkQuery\Interfaces\ILimit;

class UpdateBuilder extends BaseBuilder implements IJoin, IWhere, ILimit
{

    /**
     * Array of Join structure object created by JoinTable manipulation class
     */
    private $join = [];

    /**
     * Array of Clause structure object created by Where manipulation class
     */
    private $where = [];

    /**
     * Limit structure object created by Limit manipulating class
     */
    private $limit = null;

    /**
     * Interface IJoin required method
     */
    public function getJoin(): array
    {
        return $this->join;
    }

    /**
     * Interface IJoin required method
     */
    public function lastJoin()
    {
        $len = count($this->join);
        if ($len > 0) {
            return $this->join[$len-1];
        } elseif (isset($this->table)) {
            return $this->table;
        } else {
            return null;
        }
    }

    /**
     * Interface IJoin required method
     */
    public function countJoin(): int
    {
        return count($this->join);
    }

    /**
     * Interface IJoin required method
     */
    public function addJoin(Join $join)
    {
        $this->join[] = $join;
    }

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
