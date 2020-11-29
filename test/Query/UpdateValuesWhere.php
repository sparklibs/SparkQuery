<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__.'/../../vendor/autoload.php';

use SparkQuery\QueryBuilder;
use SparkQuery\QueryTranslator;

$sparkQuery = new QueryBuilder(QueryTranslator::TRANSLATOR_MYSQL, QueryTranslator::PARAM_ASSOC);

$builder = $sparkQuery
    ->update('table')
    ->values(['col1' => 'val1', 'col2' => 'val2', 'col3' => 'val3'])
    ->where('col1', '>=', 0)
    ->beginAndWhere()
    ->where('col2', '=', 'string')
    ->where('col3', 'IN', ['value0', 'value1', 'value2'])
    ->endWhere()
;

var_dump([
    'query' => $builder->query(),
    'params' => $builder->params()
]);
