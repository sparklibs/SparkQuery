<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../../vendor/autoload.php';

use SparkQuery\QueryBuilder;
use SparkQuery\QueryTranslator;

$sparkQuery = new QueryBuilder(QueryTranslator::TRANSLATOR_MYSQL, QueryTranslator::PARAM_ASSOC);

$builder = $sparkQuery
    ->select('table')
    ->columns(['col1', 'alias2' => 'col2', 'max' => 'MAX(col3)'])
;

var_dump([
    'query' => $builder->query(),
    'params' => $builder->params()
]);
