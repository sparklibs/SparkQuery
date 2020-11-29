<?php

namespace SparkQuery;

use SparkQuery\Query\BaseQuery;
use SparkQuery\Query\QueryObject;
use SparkQuery\Builder\SelectBuilder;
use SparkQuery\Builder\InsertBuilder;
use SparkQuery\Builder\UpdateBuilder;
use SparkQuery\Builder\DeleteBuilder;

class QueryTranslator
{

    /**
     * Query object to store translated query
     * @var $query
     */
    private $query;

    /**
     * Default query translator option
     * @var $translator
     */
    private $translator;

    /** Default translator options */
    public const TRANSLATOR_GENERIC = 1;
    /** Default translator options */
    public const TRANSLATOR_BEAUTIFY = 2;
    /** Default translator options */
    public const TRANSLATOR_MYSQL = 3;
    /** Default translator options */
    public const TRANSLATOR_SQLITE = 4;

    /**
     * Default param binding mode option
     */
    private $bindingOption;

    /** Param binding mode options */
    public const NO_PARAM = 1;
    /** Param binding mode options */
    public const PARAM_NUM = 2;
    /** Param binding mode options */
    public const PARAM_ASSOC = 3;

    /**
     * Constructor. Set options and create query object
     */
    public function __construct(int $translator = self::TRANSLATOR_MYSQL, int $bindingOption = self::PARAM_NUM)
    {
        $this->query = new QueryObject;
        $this->translator = $translator;
        $this->bindingOption = $bindingOption;
    }

    /**
     * Return the current query object
     * @return QueryObject
     */
    public function queryObject()
    {
        return $this->query;
    }

    /**
     * Pass builder obect to be translated and return query with selected binding parameter option
     * @param BaseQuery $queryBuilder
     * @param int $translator
     * @param int $bindingMode
     * @return string
     */
    public function translate(BaseQuery $queryBuilder, int $translator = 0, int $bindingOption = 0)
    {
        if ($translator == 0) {
            $translator = $this->translator;
        }
        $builderObject = $queryBuilder->getBuilder();
        $this->translateBuilder($this->query, $builderObject, $translator);
        return $this->query($bindingOption);
    }

    /**
     * Translate builder object to query object using selected translator
     * @param BaseQuery $queryBuilder
     * @param int $translator
     * @return QueryObject
     */
    public static function translateBuilder(QueryObject $queryObject, $builderObject, int $translator)
    {
        $translatorClass = self::getTranslator($translator);
        switch (true) {
            case $builderObject instanceof SelectBuilder:
                $translatorClass::translateSelect($queryObject, $builderObject);
                break;
            case $builderObject instanceof InsertBuilder:
                $translatorClass::translateInsert($queryObject, $builderObject);
                break;
            case $builderObject instanceof UpdateBuilder:
                $translatorClass::translateUpdate($queryObject, $builderObject);
                break;
            case $builderObject instanceof DeleteBuilder:
                $translatorClass::translateDelete($queryObject, $builderObject);
                break;
            default:
                throw new \Exception('Tried to translate unregistered builder object');
        }
    }

    /**
     * Get selected translator instance
     * @param mixed $builderObject
     */
    private static function getTranslator(int $translator)
    {
        switch ($translator) {
            case self::TRANSLATOR_GENERIC:
                return 'SparkQuery\\Translator\\GenericTranslator';
            // case self::TRANSLATOR_BEAUTIFY:
            //     return 'SparkQuery\\Translator\\BeautifyTranslator';
            case self::TRANSLATOR_MYSQL:
                return 'SparkQuery\\Translator\\MySQLTranslator';
            // case self::TRANSLATOR_SQLITE:
            //     return 'SparkQuery\\Translator\\SQLiteTranslator';
            default:
                throw new \Exception('Translator selected is not registered');
        }
    }

    /**
     * Get binding flag and mode from option
     */
    private static function getBindingOption(int $bindingOption)
    {
        switch ($bindingOption) {
            case self::PARAM_ASSOC:
                $bindingFlag = true;
                $bindingMode = true;
                break;
            case self::PARAM_NUM:
                $bindingFlag = true;
                $bindingMode = false;
                break;
            default:
            $bindingFlag = false;
            $bindingMode = false;
        }
        return [$bindingFlag, $bindingMode];
    }

    /**
     * Get query from current query object with input option
     * @param $bindingOption
     * @return string
     */
    public function query(int $bindingOption = 0)
    {
        if ($bindingOption == 0) {
            $bindingOption = $this->bindingOption;
        }
        return self::getQuery($this->query, $bindingOption);
    }

    /**
     * Get query string from input query object
     * @param QueryObject $queryObject
     * @return string
     */
    public static function getQuery(QueryObject $queryObject, int $bindingOption = 0)
    {
        list($bindingFlag, $bindingMode) = self::getBindingOption($bindingOption);
        $query = '';
        $params = $queryObject->params();
        $length = count($params);
        $mark = $bindingMode ? $queryObject->bindMarkAssoc() : $queryObject->bindMarkNum();
        $quote = $queryObject->stringQuote();

        foreach ($queryObject->parts() as $i => $part) {
            $query .= $part;
            if ($i < $length) {
                $query .= $bindingFlag
                    ? ($bindingMode ? $mark. 'v'. $i : $mark)
                    : (is_string($params[$i]) ? $quote. $params[$i]. $quote : $params[$i])
                ;
            }
        }
        return $query;
    }

    /**
     * Get params from current query object with input option
     * @param $bindingOption
     * @return array
     */
    public function params(int $bindingOption = 0)
    {
        if ($bindingOption == 0) {
            $bindingOption = $this->bindingOption;
        }
        return  self::getParams($this->query, $bindingOption);
    }

    /**
     * Get params from current query object with input option
     * @param $option
     * @return array
     */
    public static function getParams(QueryObject $queryObject, int $bindingOption = 0)
    {
        list($bindingFlag, $bindingMode) = self::getBindingOption($bindingOption);
        $array = [];

        if ($bindingFlag) {
            if ($bindingMode) {
                foreach ($queryObject->params() as $i => $param) {
                    $array['v'.$i] = $param;
                }
            } else {
                $array = $queryObject->params();
            }
        }
        return $array;
    }

}
