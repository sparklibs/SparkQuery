<?php

namespace SparkQuery\Interfaces;

use SparkQuery\Builder\BaseBuilder;

interface IQuery
{

    /** 
     * Get builder object
     */
    public function getBuilder(): BaseBuilder;

}
