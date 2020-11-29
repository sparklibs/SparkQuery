<?php

namespace SparkQuery\Interfaces;

interface ITranslator
{

    /**
     * Translate select builder object to SELECT query object
     * @param QueryObject $query
     * @param SelectBuilder $builder
     */
    public static function translateSelect($query, $builder);

    /**
     * Translate insert builder object to INSERT query object
     * @param QueryObject $query
     * @param InsertBuilder $builder
     */
    public static function translateInsert($query, $builder);

    /**
     * Translate update builder object to UPDATE query object
     * @param QueryObject $query
     * @param UpdateBuilder $builder
     */
    public static function translateUpdate($query, $builder);

    /**
     * Translate delete builder object to DELETE query object
     * @param QueryObject $query
     * @param DeleteBuilder $builder
     */
    public static function translateDelete($query, $builder);

}
