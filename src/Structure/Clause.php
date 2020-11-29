<?php

namespace SparkQuery\Structure;

class Clause
{

    /**
     * Column object
     */
    private $column;

    /**
     * Clause operator
     */
    private $operator;

    /** Operator type */
    public const OPERATOR_DEFAULT = 0;
    /** Operator type */
    public const OPERATOR_EQUAL = 1;
    /** Operator type */
    public const OPERATOR_NOT_EQUAL = 2;
    /** Operator type */
    public const OPERATOR_GREATER = 3;
    /** Operator type */
    public const OPERATOR_GREATER_EQUAL = 4;
    /** Operator type */
    public const OPERATOR_LESS = 5;
    /** Operator type */
    public const OPERATOR_LESS_EQUAL = 6;
    /** Operator type */
    public const OPERATOR_LIKE = 7;
    /** Operator type */
    public const OPERATOR_NOT_LIKE = 8;
    /** Operator type */
    public const OPERATOR_BETWEEN = 9;
    /** Operator type */
    public const OPERATOR_NOT_BETWEEN = 10;
    /** Operator type */
    public const OPERATOR_IN = 11;
    /** Operator type */
    public const OPERATOR_NOT_IN = 12;
    /** Operator type */
    public const OPERATOR_NULL = 13;
    /** Operator type */
    public const OPERATOR_NOT_NULL = 14;

    /**
     * Clause comparison values
     */
    private $values;

    /**
     * Clause conjunctive
     */
    private $conjunctive;

    /**
     * Conjunctive for build nested clause
     */
    private $nestedConjunctive;

    /** Conjunctive type */
    public const CONJUNCTIVE_BEGIN = 1;
    /** Conjunctive type */
    public const CONJUNCTIVE_AND_BEGIN = 2;
    /** Conjunctive type */
    public const CONJUNCTIVE_OR_BEGIN = 3;
    /** Conjunctive type */
    public const CONJUNCTIVE_NOT_AND_BEGIN = 4;
    /** Conjunctive type */
    public const CONJUNCTIVE_NOT_OR_BEGIN = 5;
    /** Conjunctive type */
    public const CONJUNCTIVE_AND = 6;
    /** Conjunctive type */
    public const CONJUNCTIVE_OR = 7;
    /** Conjunctive type */
    public const CONJUNCTIVE_NOT_AND = 8;
    /** Conjunctive type */
    public const CONJUNCTIVE_NOT_OR = 9;
    /** Conjunctive type */
    public const CONJUNCTIVE_NONE = 10;
    /** Conjunctive type */
    public const CONJUNCTIVE_END = 11;

    /**
     * Constructor. Clear all properties
     */
    public function __construct($column, int $operator, $values, int $conjunctive, int $nestedConjunctive)
    {
        $this->column = $column;
        $this->operator = ($operator >= 1 && $operator <= 14) ? $operator : self::OPERATOR_DEFAULT;
        $this->values = $values;
        $this->conjunctive = ($conjunctive >= 6 && $conjunctive <= 9) ? $conjunctive : self::CONJUNCTIVE_NONE;
        $this->nestedConjunctive = $nestedConjunctive;
    }

    /** Edit nested conjunctive type */
    public function editNestedConjunctive($nestedConjunctive)
    {
        $this->nestedConjunctive = $nestedConjunctive;
    }

    /** Get clause column */
    public function column()
    {
        return $this->column;
    }

    /** Get operator type */
    public function operator(): int
    {
        return $this->operator;
    }

    /** Get clause comparison values */
    public function values()
    {
        return $this->values;
    }

    /** Get conjunctive type */
    public function conjunctive(): int
    {
        return $this->conjunctive;
    }

    /** Get nested conjunctive type */
    public function nestedConjunctive(): int
    {
        return $this->nestedConjunctive;
    }

}