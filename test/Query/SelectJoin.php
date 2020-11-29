<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../../vendor/autoload.php';

use SparkQuery\QueryBuilder;
use SparkQuery\QueryTranslator;

$sparkQuery = new QueryBuilder(QueryTranslator::TRANSLATOR_MYSQL, QueryTranslator::PARAM_ASSOC);

$builder = $sparkQuery
    ->select('baseTable')
    ->columns(['col1', 'col2', 'MAX(col3)'])
    ->leftJoin('joinTable')
    ->on('baseCol1', 'joinCol1')
    ->on('baseCol2', 'joinCol2')
    ->columns(['col1', 'col2'])
;

var_dump([
    'query' => $builder->query(),
    'params' => $builder->params()
]);
