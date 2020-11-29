<?php

namespace SparkQuery\Structure;

use SparkQuery\Structure\Column;

class Order
{

    /**
     * Column object
     */
    private $column;

    /**
     * Order type
     */
    private $orderType;

    /** Order type */
    public const ORDER_NONE = 0;
    /** Order type */
    public const ORDER_ASC = 1;
    /** Order type */
    public const ORDER_DESC = 2;

    /**
     * Constructor. Order column and order type
     */
    public function __construct(Column $column, int $orderType)
    {
        $this->column = $column;
        $this->orderType = ($orderType >= 1 && $orderType <=2) ? $orderType : self::ORDER_NONE;
    }

    /** Get order column */
    public function column(): Column
    {
        return $this->column;
    }

    /** Get order type */
    public function orderType(): int
    {
        return $this->orderType;
    }

}