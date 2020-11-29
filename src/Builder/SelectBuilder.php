<?php

namespace SparkQuery\Builder;

use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Structure\Join;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Order;
use SparkQuery\Structure\Limit;
use SparkQuery\Interfaces\IJoin;
use SparkQuery\Interfaces\IWhere;
use SparkQuery\Interfaces\IGroupBy;
use SparkQuery\Interfaces\IHaving;
use SparkQuery\Interfaces\IOrderBy;
use SparkQuery\Interfaces\ILimit;

class SelectBuilder extends BaseBuilder implements IJoin, IWhere, IGroupBy, IHaving, IOrderBy, ILimit
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
     * Array of Column structure object created by GroupBy manipulation class
     */
    private $groupBy = [];

    /**
     * Array of Clause structure object created by Having manipulation class
     */
    private $having = [];

    /**
     * Array of OrderBy structure object created by OrderBy manipulation class
     */
    private $orderBy = [];

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
     * Interface Ihaving required method
     */
    public function getHaving(): array
    {
        return $this->having;
    }

    /**
     * Interface Ihaving required method
     */
    public function lastHaving()
    {
        $len = count($this->having);
        if ($len > 0) {
            return $this->having[$len-1];
        } else {
            return null;
        }
    }

    /**
     * Interface Ihaving required method
     */
    public function countHaving(): int
    {
        return count($this->having);
    }

    /**
     * Interface Ihaving required method
     */
    public function addHaving(Clause $having)
    {
        $this->having[] = $having;
    }

    /**
     * Interface IGroupBy required method
     */
    public function getGroup(): array
    {
        return $this->groupBy;
    }

    /**
     * Interface IGroupBy required method
     */
    public function countGroup(): int
    {
        return count($this->groupBy);
    }

    /**
     * Interface IGroupBy required method
     */
    public function addGroup($group)
    {
        $this->groupBy[] = $group;
    }

    /**
     * Interface IOrderBy required method
     */
    public function getOrder(): array
    {
        return $this->orderBy;
    }

    /**
     * Interface IOrderBy required method
     */
    public function countOrder(): int
    {
        return count($this->orderBy);
    }

    /**
     * Interface IOrderBy required method
     */
    public function addOrder(Order $order)
    {
        $this->orderBy[] = $order;
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
