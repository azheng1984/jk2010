<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $instances = array();
    private static $client;
    private $name;

    public static function getColumnById($id, $columnName) {
        return static::getClient()->getColumnById(
            static::getTableName(), $id, $columnName
        );
    }

    public static function getColumnByColumns($columns, $columnName) {
        return static::getClient()->getColumnByColumns(
            static::getTableName(), $columns, $columnName
        );
    }

    public static function getRowById($id, array $columnNames = null) {
        return static::getClient()->getRowById(
            static::getTableName(), $id, $columnNames
        );
    }

    public static function getCacheById($id, $mixed = null) {
    }

    public static function deleteCacheById($id) {
    }

    public static function getRowByColumns(
        $columns, array $columnNames = null
    ) {
        return static::getClient()->getRowByColumns(
            static::getTableName(), $columns, $columnNames
        );
    }

    public static function getAllByColumns(
        $columns, array $columnNames = null
    ) {
        return static::getClient()->getAllByColumns(
            static::getTableName(), $columns, $columnNames
        );
    }

    public static function count() {
        return static::getClient()->count(static::getTableName());
    }

    public static function min($columnName) {
        return static::getClient()->min(static::getTableName(), $columnName);
    }

    public static function max($columnName) {
        return static::getClient()->max(static::getTableName(), $columnName);
    }

    public static function sum($columnName) {
        return static::getClient()->sum(static::getTableName(), $columnName);
    }

    public static function average($columnName) {
        return static::getClient()->average(
            static::getTableName(), $columnName
        );
    }

    public static function save(array &$row) {
        return static::getClient()->save(static::getTableName(), $row);
    }

    public static function deleteById($id) {
        return static::getClient()->deleteById(static::getTableName(), $id);
    }

    protected static function getClient() {
        if (self::$client === null) {
            self::$client = new DbClient;
        }
        return self::$client;
    }

    protected static function getTableName() {
        $instance = self::getInstance();
        if ($instance->name === null) {
            $class = get_called_class();
            $position = strrpos($class, '\\');
            if ($position !== false) {
                $class = substr($class, $position + 1);
            }
            if (strncmp('Db', $class, 2) !== 0) {
                throw new Exception;
            }
            $instance->name = substr($class, 2);
        }
        return $instance->name;
    }

    final protected static function getInstance() {
        $class = get_called_class();
        if (isset(self::$instances[$class]) === false) {
            self::$instances[$class] = new $class;
        }
        return self::$instances[$class];
    }
}
