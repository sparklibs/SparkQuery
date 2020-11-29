<?php

namespace SparkQuery\Translator;

use SparkQuery\Builder\BaseBuilder;
use SparkQuery\Structure\Table;
use SparkQuery\Structure\Column;
use SparkQuery\Structure\Value;
use SparkQuery\Structure\Expression;
use SparkQuery\Structure\Join;
use SparkQuery\Structure\Clause;
use SparkQuery\Structure\Order;
use SparkQuery\Structure\Limit;
use SparkQuery\Interfaces\ITranslator;

class GenericTranslator implements ITranslator
{

    public const QUOTE = "";

    public const EQUAL = '=';

    public const OPEN_BRACKET = '(';

    public const CLOSE_BRACKET = ')';

    public const DOT = '.';

    public const COMMA = ', ';

    public const END_QUERY = ';';

    /** ITranslator required method */
    public static function translateSelect($query, $builder)
    {
        $multiTableFlag = boolval($builder->countJoin());

        self::firstKeyword($query, $builder->builderType());
        self::columnList($query, $builder->getColumns(), $builder->countColumns(), $multiTableFlag);
        self::fromTable($query, $builder->getTable());
        self::join($query, $builder->getJoin());
        self::where($query, $builder->getWhere(), $builder->countWhere(), $multiTableFlag);
        self::groupBy($query, $builder->getGroup(), $builder->countGroup(), $multiTableFlag);
        self::having($query, $builder->getHaving(), $builder->countHaving(), $multiTableFlag);
        self::orderBy($query, $builder->getOrder(), $builder->countOrder(), $multiTableFlag);
        self::limitOffset($query, $builder->getLimit(), $builder->hasLimit());
        $query->add(self::END_QUERY);
    }

    /** ITranslator required method */
    public static function translateInsert($query, $builder)
    {
        self::firstKeyword($query, $builder->builderType());
        self::intoTable($query, $builder->getTable());
        self::columnListInsert($query, $builder->getValues());
        self::valuesInsert($query, $builder->getValues(), $builder->countValues());
        $query->add(self::END_QUERY);
    }

    /** ITranslator required method */
    public static function translateUpdate($query, $builder)
    {
        self::firstKeyword($query, $builder->builderType());
        self::tableSet($query, $builder->getTable());
        self::valuesUpdate($query, $builder->getValues());
        self::where($query, $builder->getWhere(), $builder->countWhere());
        self::limitOffset($query, $builder->getLimit(), $builder->hasLimit());
        $query->add(self::END_QUERY);
    }

    /** ITranslator required method */
    public static function translateDelete($query, $builder)
    {
        self::firstKeyword($query, $builder->builderType());
        self::fromTable($query, $builder->getTable());
        self::where($query, $builder->getWhere(), $builder->countWhere());
        self::limitOffset($query, $builder->getLimit(), $builder->hasLimit());
        $query->add(self::END_QUERY);
    }

    private static function firstKeyword($query, $builderType)
    {
        switch ($builderType) {
            case BaseBuilder::SELECT:
                $query->add('SELECT ');
                break;
            case BaseBuilder::INSERT:
                $query->add('INSERT ');
                break;
            case BaseBuilder::UPDATE:
                $query->add('UPDATE ');
                break;
            case BaseBuilder::DELETE:
                $query->add('DELETE ');
                break;
            case BaseBuilder::SELECT:
                $query->add('SELECT ');
                break;
            case BaseBuilder::SELECT_DISTINCT:
                $query->add('SELECT DISTINCT ');
                break;
        }
    }

    private static function fromTable($query, $table)
    {
        if ($table instanceof Table) {
            $name = $table->name();
            $alias = $table->alias();

            $query->add(' FROM '. self::QUOTE);
            $query->add($table->name());
            $query->add(self::QUOTE);
            if ($alias) {
                $query->add(' AS '. self::QUOTE. $alias. self::QUOTE);
            }
        }
    }

    private static function intoTable($query, $table)
    {
        if ($table instanceof Table) {
            $name = $table->name();

            $query->add('INTO '. self::QUOTE);
            $query->add($table->name());
            $query->add(self::QUOTE);
        }
    }

    private static function tableSet($query, $table)
    {
        if ($table instanceof Table) {
            $name = $table->name();

            $query->add(self::QUOTE);
            $query->add($table->name());
            $query->add(self::QUOTE. ' SET ');
        }
    }

    private static function columnList($query, array $columns, int $count, bool $multiTableFlag = false)
    {
        if ($count == 0) {
            $query->add('*');
            return;
        }
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                $table = $column->table();
                $name = $column->name();
                $alias = $column->alias();
                $function = $column->function();

                if ($function) {
                    $query->add($function. self::OPEN_BRACKET);
                }
                if ($table && $multiTableFlag) {
                    $query->add(self::QUOTE);
                    $query->add($table);
                    $query->add(self::QUOTE. self::DOT);
                }
                $query->add(self::QUOTE);
                $query->add($name);
                $query->add(self::QUOTE);
                if ($function) {
                    $query->add(self::CLOSE_BRACKET);
                }
                if ($alias) {
                    $query->add(' AS '. self::QUOTE. $alias. self::QUOTE);
                }
                (--$count < 1) ?: $query->add(self::COMMA);
            } else {
                self::expression($query, $column);
                (--$count < 1) ?: $query->add(self::COMMA);
            }
        }
    }

    private static function columnListInsert($query, array $values)
    {
        $value = $values[0];
        if ($value instanceof Value) {
            $columns = $value->columns();
            $count = count($columns);

            $query->add(' '. self::OPEN_BRACKET);
            foreach ($columns as $column) {
                $query->add(self::QUOTE);
                $query->add($column);
                $query->add(self::QUOTE);
                (--$count < 1) ?: $query->add(self::COMMA);
            }
            $query->add(self::CLOSE_BRACKET);
        }
    }

    private static function column($query, $column, bool $tableFlag = false)
    {
        if ($column instanceof Column) {
            $table = $column->table();
            $name = $column->name();
            $alias = $column->alias();
            $function = $column->function();

            if ($function) {
                $query->add($function. self::OPEN_BRACKET);
            }
            if ($alias) {
                $query->add(self::QUOTE);
                $query->add($column->alias());
                $query->add(self::QUOTE);
            } else {
                $query->add(self::QUOTE);
                if ($table && $tableFlag) {
                    $query->add($table);
                    $query->add(self::QUOTE. self::DOT. self::QUOTE);
                }
                $query->add($name);
                $query->add(self::QUOTE);
            }
            if ($function) {
                $query->add(self::CLOSE_BRACKET);
            }
            return true;
        } else {
            return false;
        }
    }

    private static function valuesInsert($query, array $values, int $count)
    {
        $query->add(' VALUES ');
        $countInit = $count;
        $countInit <= 1 ?: $query->add(self::OPEN_BRACKET);

        foreach ($values as $value) {
            if ($value instanceof Value) {
                $vals = $value->values();
                $countVals = count($vals);

                $query->add(self::OPEN_BRACKET);
                foreach ($vals as $val) {
                    $query->add($val, true);
                    (--$countVals < 1) ?: $query->add(self::COMMA);
                }
                $query->add(self::CLOSE_BRACKET);
            }
            (--$count < 1) ?: $query->add(self::COMMA);
        }
        $countInit <= 1 ?: $query->add(self::CLOSE_BRACKET);
    }

    private static function valuesUpdate($query, array $values)
    {
        $value = $values[0];
        if ($value instanceof Value) {
            $columns = $value->columns();
            $vals = $value->values();
            $count = count($vals);

            foreach ($vals as $i => $val) {
                $query->add(self::QUOTE);
                $query->add($columns[$i]);
                $query->add(self::QUOTE. self::EQUAL);
                $query->add($val, true);
                (--$count < 1) ?: $query->add(self::COMMA);
            }
        }
    }

    private static function expression($query, $expression)
    {
        if ($expression instanceof Expression) {
            $params = $expression->params();
            $exps = $expression->expression();
            $alias = $expression->alias();

            foreach ($exps as $i => $exp) {
                $query->add($exp);
                empty($params[$i]) ?: $query->add($params[$i], true);
            }
            if ($alias) {
                $query->add(' AS '. self::QUOTE);
                $query->add($alias);
                $query->add(self::QUOTE);
            }
        }
    }

    private static function join($query, array $joins)
    {
        foreach ($joins as $join) {
            if ($join instanceof Join) {
                switch ($join->joinType()) {
                    case Join::INNER_JOIN:
                        $query->add(' INNER JOIN '. self::QUOTE);
                        break;
                    case Join::LEFT_JOIN:
                        $query->add(' LEFT JOIN '. self::QUOTE);
                        break;
                    case Join::RIGHT_JOIN:
                        $query->add(' RIGHT JOIN '. self::QUOTE);
                        break;
                    case Join::OUTER_JOIN:
                        $query->add(' OUTER JOIN '. self::QUOTE);
                }
            }
            self::joinTable($query, $join->joinTable(), $join->joinAlias());
            self::joinColumns($query, $join->baseColumns(), $join->joinColumns(), $join->usingColumns());
        }
    }

    private static function joinTable($query, $joinTable, $joinAlias)
    {
        $query->add($joinTable);
        if ($joinAlias) {
            $query->add(self::QUOTE. ' AS '. self::QUOTE. $joinAlias);
        }
        $query->add(self::QUOTE);
    }

    private static function joinColumns($query, $baseColumns, $joinColumns, $usingColumns)
    {    
        $count = count($usingColumns);
        if ($count) {
            $query->add(' USING '. self::OPEN_BRACKET);
            foreach ($usingColumns as $column) {
                self::column($query, $column, false);
                (--$count < 1) ?: $query->add(self::COMMA);
            }
            $query->add(self::CLOSE_BRACKET);
        } else {
            foreach ($baseColumns as $i => $baseCol) {
                $i == 0 ? $query->add(' ON ') : $query->add(' AND ');
                self::column($query, $baseCol);
                $query->add(self::EQUAL);
                self::column($query, $joinColumns[$i]);
            }
        }
    }

    private static function operator(int $operator): string
    {
        switch ($operator) {
            case Clause::OPERATOR_EQUAL:
                return '=';
            case Clause::OPERATOR_NOT_EQUAL:
                return '!=';
            case Clause::OPERATOR_GREATER:
                return '>';
            case Clause::OPERATOR_GREATER_EQUAL:
                return '>=';
            case Clause::OPERATOR_LESS:
                return '<';
            case Clause::OPERATOR_LESS_EQUAL:
                return '<=';
            case Clause::OPERATOR_BETWEEN:
                return ' BETWEEN ';
            case Clause::OPERATOR_NOT_BETWEEN:
                return ' NOT BETWEEN ';
            case Clause::OPERATOR_LIKE:
                return 'LIKE';
            case Clause::OPERATOR_NOT_LIKE:
                return 'NOT LIKE';
            case Clause::OPERATOR_IN:
                return ' IN ';
            case Clause::OPERATOR_NOT_IN:
                return ' NOT IN ';
            case Clause::OPERATOR_NULL:
                return 'NULL';
            case Clause::OPERATOR_NOT_NULL:
                return 'NOT NULL';
            default:
                return '';
        }
    }

    private static function conjunctive(int $conjunctive): string
    {
        switch ($conjunctive) {
            case Clause::CONJUNCTIVE_AND:
                return ' AND ';
            case Clause::CONJUNCTIVE_OR:
                return ' OR ';
            case Clause::CONJUNCTIVE_NOT_AND:
                return ' NOT AND ';
            case Clause::CONJUNCTIVE_NOT_OR:
                return ' NOT OR ';
            default:
                return '';
        }
    }

    private static function nestedConjunctive(int $nestedConjunctive)
    {
        switch ($nestedConjunctive) {
            case Clause::CONJUNCTIVE_BEGIN:
                return self::OPEN_BRACKET;
            case Clause::CONJUNCTIVE_AND_BEGIN:
                return ' AND '. self::OPEN_BRACKET;
            case Clause::CONJUNCTIVE_OR_BEGIN:
                return ' OR '. self::OPEN_BRACKET;
            case Clause::CONJUNCTIVE_NOT_AND_BEGIN:
                return ' NOT AND '. self::OPEN_BRACKET;
            case Clause::CONJUNCTIVE_NOT_OR_BEGIN:
                return ' NOT OR '. self::OPEN_BRACKET;
            case Clause::CONJUNCTIVE_END:
                return self::CLOSE_BRACKET;
        }
        if ($nestedConjunctive < Clause::CONJUNCTIVE_BEGIN) {
            $open = self::OPEN_BRACKET;
            for ($i = $nestedConjunctive; $i < Clause::CONJUNCTIVE_BEGIN; $i++) {
                $open .= self::OPEN_BRACKET;
            }
            return $open;
        } elseif ($nestedConjunctive > Clause::CONJUNCTIVE_END) {
            $close = self::CLOSE_BRACKET;
            for ($i = $nestedConjunctive; $i > Clause::CONJUNCTIVE_END; $i--) {
                $close .= self::CLOSE_BRACKET;
            }
            return $close;
        }
        return '';
    }

    private static function where($query, array $whereClauses, $count, $multiTableFlag = false)
    {
        if ($count) {
            $query->add(' WHERE ');
            foreach ($whereClauses as $where) {
                $nCon = $where->nestedConjunctive();
                $nestedConjunctive = self::nestedConjunctive($nCon);
                $conjunctive = self::conjunctive($where->conjunctive());

                $nCon >= Clause::CONJUNCTIVE_END ?: $query->add($nestedConjunctive);
                $query->add($conjunctive);
                self::clause($query, $where, $multiTableFlag);
                $nCon < Clause::CONJUNCTIVE_END ?: $query->add($nestedConjunctive);
            }
        }
    }

    private static function having($query, array $havingClauses, $count, $multiTableFlag = false)
    {
        if ($count) {
            $query->add(' HAVING ');
            foreach ($havingClauses as $having) {
                $nCon = $having->nestedConjunctive();
                $nestedConjunctive = self::nestedConjunctive($nCon);
                $conjunctive = self::conjunctive($having->conjunctive());
    
                $nCon == Clause::CONJUNCTIVE_END ?: $query->add($nestedConjunctive);
                $query->add($conjunctive);
                self::clause($query, $having, $multiTableFlag);
                $nCon != Clause::CONJUNCTIVE_END ?: $query->add($nestedConjunctive);
            }
        }
    }

    private static function clause($query, $clause, $multiTableFlag = false)
    {
        if ($clause instanceof Clause) {
            self::column($query, $clause->column(), $multiTableFlag) ?: self::expression($query, $clause->column());
            $operator = $clause->operator();
            $values = $clause->values();

            switch ($operator) {
                case Clause::OPERATOR_BETWEEN:
                case Clause::OPERATOR_NOT_BETWEEN:
                    self::between($query, $operator, $values);
                    break;
                case Clause::OPERATOR_IN:
                case Clause::OPERATOR_NOT_IN:
                    self::in($query, $operator, $values);
                    break;
                case Clause::OPERATOR_NULL:
                    $query->add(' IS NULL');
                    break;
                case Clause::OPERATOR_NOT_NULL:
                    $query->add(' IS NOT NULL');
                    break;
                default:
                    self::comparison($query, $operator, $values);
                    break;
            }
        }
    }

    private static function comparison($query, int $operator, $values)
    {
        $operatorString = self::operator($operator);
        $query->add($operatorString);
        $query->add($values, true);
    }

    private static function between($query, int $operator, array $values)
    {
        $operatorString = self::operator($operator);
        $query->add($operatorString);
        $query->add($values[0], true);
        $query->add(' AND ');
        $query->add($values[1], true);
    }

    private static function in($query, int $operator, array $values)
    {
        $operatorString = self::operator($operator);
        $query->add($operatorString. self::OPEN_BRACKET);
        $count = count($values);
        foreach ($values as $value) {
            $query->add($value, true);
            (--$count < 1) ?: $query->add(self::COMMA);
        }
        $query->add(self::CLOSE_BRACKET);
    }

    private static function groupBy($query, array $groups, int $count, $multiTableFlag = false)
    {
        if ($count) {
            $query->add(' GROUP BY ');
            foreach ($groups as $group) {
                self::column($query, $group, $multiTableFlag);
                (--$count < 1) ?: $query->add(self::COMMA);
            }
        }
    }

    private static function orderBy($query, array $orders, int $count, $multiTableFlag = false)
    {
        if ($count) {
            $query->add(' ORDER BY ');
            foreach ($orders as $order) {
                $column = $order->column();
                self::column($query, $column, $multiTableFlag);
    
                switch ($order->orderType()) {
                    case Order::ORDER_ASC:
                        $query->add(' ASC');
                        break;
                    case Order::ORDER_DESC:
                        $query->add(' DESC');
                }
                (--$count < 1) ?: $query->add(self::COMMA);
            }
        }
    }

    private static function limitOffset($query, $limitOffset, bool $hasLimit)
    {
        if ($hasLimit) {
            $limit = $limitOffset->limit();
            $offset = $limitOffset->offset();
            if ($limit == Limit::NOT_SET) {
                $query->add(' OFFSET ');
                $query->add($offset, true);
            } else {
                $query->add(' LIMIT ');
                $query->add($limit, true);
                if ($offset != Limit::NOT_SET) {
                    $query->add(self::COMMA);
                    $query->add($offset, true);
                }
            }
        }
    }

}
