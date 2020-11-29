<?php

namespace SparkQuery\Query\Manipulation;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Interfaces\IOrderBy;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Order;

class OrderBy extends BaseQuery
{

    /**
     * Constructor.
     * Set the builder object
     */
    public function __construct($builderObject, $table = '', array $options = [], $statement = null)
    {
        if ($builderObject instanceof IOrderBy) {
            $this->builder = $builderObject;
            $this->table = $table;
            $this->options = $options;
            $this->statement = $statement;
        } else {
            throw new \Exception('Builder object not support OrderBy manipulation');
        }
    }

    /**
     * Call function for non-exist method calling.
     * Used for invoking next manipulation method in different class
     */
    public function __call($function, $arguments)
    {
        return $this->callQuery($function, $arguments);
    }

    /**
     * Creating Order object
     * @param Column $column
     * @param mixed $orderType
     * @return Order
     */
    private function createOrder($column, $orderType)
    {
        $columnObject = $this->createColumn($column);
        $validType = $this->getOrderType($orderType);
        $orderObject = new Order($columnObject, $validType);
        return $orderObject;
    }

    /**
     * Get valid order type from input order type
     * @param mixed $orderType
     * @return int
     */
    private function getOrderType($orderType)
    {
        if (is_int($orderType)) {
            $validType = $orderType;
        } else {
            switch ($orderType) {
                case 'ascending':
                case 'asc':
                case 'ASCENDING':
                case 'ASC':
                    $validType = Order::ORDER_ASC;
                break;
                case 'descending':
                case 'desc':
                case 'DESCENDING':
                case 'DESC':
                    $validType = Order::ORDER_DESC;
                break;
                default:
                    $validType = Order::ORDER_NONE;
            }
        }
        return $validType;
    }

    /**
     * ORDER BY query manipulation
     * @param mixed $column
     * @param mixed $orderType
     * @return this
     */
    public function orderBy($column, $orderType = Order::ORDER_NONE)
    {
        $order = $this->createOrder($column, $orderType);
        $this->builder->addOrder($order);
        return $this;
    }

    /**
     * ORDER BY query manipulation with multiple inputs
     * @param array $orders
     * @return this
     */
    public function ordersBy(array $orders)
    {
        foreach ($orders as $column => $type) {
            $order = $this->createOrder($column, $type);
            $this->builder->addOrder($order);
        }
        return $this;
    }

    /**
     * ORDER BY query manipulation using ASC order
     * @param mixed $column
     * @return this
     */
    public function orderAsc($column)
    {
        $order = $this->createOrder($column, Order::ORDER_ASC);
        $this->builder->addOrder($order);
        return $this;
    }

    /**
     * ORDER BY query manipulation using DESC order
     * @param mixed $column
     * @return this
     */
    public function orderDesc($column)
    {
        $order = $this->createOrder($column, Order::ORDER_DESC);
        $this->builder->addOrder($order);
        return $this;
    }

}
