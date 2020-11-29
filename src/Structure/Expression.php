<?php

namespace App\Core\QueryBuilder\Structure;

class Expression
{

    /**
     * Expression string
     */
    private $expression;

    /**
     * Alias name of expression
     */
    private $alias;

    /**
     * Params to bind with placeholders
     */
    private $params;

    /**
     * Constructor. Insert expression parts, params, and alias
     */
    public function __construct(array $expression, string $alias, array $params)
    {
        $this->expression = [];
        $this->alias = $alias;
        $this->params = [];
        $lenExp = count($expression);
        $lenPar = count($params);
        $len = $lenExp < $lenPar ? $lenExp : $lenPar;
        for ($i = 0; $i < $len; $i++) {
            $this->expression[] = $expression[$i];
            $this->params[] = $params[$i];
        }
        if (isset($expression[$len])) {
            $this->expression[] = $expression[$len];
        }
    }

    /** Get array of expression string */
    public function expression(): array
    {
        return $this->expression;
    }

    /** Get alias name of expression */
    public function alias(): string
    {
        return $this->alias;
    }

    /** Get parameters array */
    public function params(): array
    {
        return $this->params;
    }

}
