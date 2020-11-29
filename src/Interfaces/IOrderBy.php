<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Structure\Order;

interface IOrderBy
{

    /**
     * Get array of Order structure object
     * @return array of Order object
     */
    public function getOrder(): array;

    /**
     * Count number of Order structure object already added in builder object
     * @return int
     */
    public function countOrder(): int;

    /**
     * Add Order structure object to orderBy property of builder object
     * @param Order $order
     */
    public function addOrder(Order $order);

}
