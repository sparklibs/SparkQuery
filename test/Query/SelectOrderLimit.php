<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../../vendor/autoload.php';

use SparkQuery\QueryBuilder;
use SparkQuery\QueryTranslator;

$sparkQuery = new QueryBuilder(QueryTranslator::TRANSLATOR_MYSQL, QueryTranslator::PARAM_ASSOC);

$builder = $sparkQuery
    ->select('table')
    ->orderAsc('col1')
    ->orderDesc('col2')
    ->limit(25, 50)
;

var_dump([
    'query' => $builder->query(),
    'params' => $builder->params()
]);
