<?php

namespace SparkQuery\Structure;

class Limit
{

    /**
     * Limit
     */
    private $limit;

    /**
     * Offset
     */
    private $offset;

    /** Not set constant */
    public const NOT_SET = -1;

    /**
     * Constructor. Set limit and offset
     */
    public function __construct(int $limit, int $offset)
    {
        $this->limit = ($limit >= 0) ? $limit : self::NOT_SET ;
        $this->offset = ($offset >= 0) ? $offset : self::NOT_SET ;
    }

    /** Get limit */
    public function limit(): int
    {
        return $this->limit;
    }

    /** Get offset */
    public function offset(): int
    {
        return $this->offset;
    }

}