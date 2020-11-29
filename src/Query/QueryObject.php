<?php

namespace SparkQuery\Query;

class QueryObject
{

    /**
     * List of query parts.
     * Contains SQL keyword string
     */
    private $parts;

    /**
     * Value parameters list
     * Contains values of INSERT or UPDATE, clause comparison values, LIMIT values, or OFFSET values
     */
    private $params;

    /**
     * Number of query parts
     */
    private $number;

    /**
     * Mark parameter of current query part already set
     */
    private $paramSet;

    /**
     * Constructor. Clear query parts and all parameters
     */
    public function __construct()
    {
        $this->parts = [];
        $this->params = [];
        $this->number = -1;
        $this->paramSet = true;
    }

    /**
     * Get array of query parts
     */
    public function parts(): array
    {
        return $this->parts;
    }

    /**
     * Get value parameters list
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Add a query part or a query parameter to the list.
     */
    public function add($queryPart, bool $paramFlag = false)
    {
        if (!$paramFlag || $this->number < 0) {
            if ($this->paramSet) {
                $this->parts[] = $queryPart;
                $this->paramSet = false;
                $this->number++;
            } else {
                $this->parts[$this->number] .= $queryPart;
            }
        } else {
            $this->params[$this->number] = $queryPart;
            $this->paramSet = true;
        }
    }

    /** Sequential parameter binding mark */
    private $bindMarkNum = '?';

    /** Associative parameter binding mark */
    private $bindMarkAssoc = ':';

    /** Quote character for enclosing string type value parameters */
    private $stringQuote = '\'';

    /** Get sequential binding mark */
    public function bindMarkNum(): string
    {
        return $this->bindMarkNum;
    }

    /** Get associative binding mark */
    public function bindMarkAssoc(): string
    {
        return $this->bindMarkAssoc;
    }

    /** Get string quote */
    public function stringQuote(): string
    {
        return $this->stringQuote;
    }

    /**
     * Set sequential and associative binding mark
     */
    public function setBindMark(string $bindMarkNum, string $bindMarkAssoc, string $stringQuote)
    {
        $this->bindMarkNum = $bindMarkNum;
        $this->bindMarkAssoc = $bindMarkAssoc;
        $this->stringQuote = $stringQuote;
    }

}
