<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../../vendor/autoload.php';

use SparkQuery\QueryBuilder;
use SparkQuery\QueryTranslator;

$sparkQuery = new QueryBuilder(QueryTranslator::TRANSLATOR_MYSQL, QueryTranslator::PARAM_ASSOC);

$builder = $sparkQuery
    ->select('table')
    ->where('col1', '>=', 0)
        ->beginAndWhere()
        ->where('col3', 'IN', ['value0', 'value1', 'value2'])
        ->orWhere('col4', 'BETWEEN', ['minValue', 'maxValue'])
        ->endWhere()
    ->groupBy('col1')
    ->having('col5', '=', 'havingValue')
;

var_dump([
    'query' => $builder->query(),
    'params' => $builder->params()
]);
