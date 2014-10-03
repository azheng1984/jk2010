<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $instances = array();
    private static $client;
    private $name;

    public static function getColumnById($id, $selector) {
        return static::getClient()->getColumnById(
            static::getTableName(), $id, $selector
        );
    }

    public static function getColumnByColumns($columns, $selector) {
        return static::getClient()->getColumnByColumns(
            static::getTableName(), $columns, $selector
        );
    }

    public static function getRowById($id, $selector = '*') {
        return static::getClient()->getRowById(
            static::getTableName(), $id, $selector
        );
    }

    public static function getRowByColumns($columns, $selector = '*') {
        return static::getClient()->getRowByColumns(
            static::getTableName(), $columns, $selector = '*'
        );
    }

    public static function getAllByColumns($columns, $selector = '*') {
        return static::getClient()->getAllByColumns(
            static::getTableName(), $columns, $selector = '*'
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

    public static function insert($row) {
        return static::getClient()->insert(
            static::getTableName(), $row
        );
    }

    public static function update($columns, $where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, static::getTableName());
        return call_user_func_array(
            array(static::getClient(), 'update'), $args
        );
    }

    public static function updateByColumns($replacementColumns, $filterColumns) {
        return static::getClient()->updateByColumns(
            static::getTableName(), $replacementColumns, $filterColumns
        );
    }

    public static function save(&$row) {
        return static::getClient()->save(
            static::getTableName(), $row
        );
    }

    public static function delete($where/*, ...*/) {
        $args = func_get_args();
        array_unshift($args, static::getTableName());
        return call_user_func_array(
            array(static::getClient(), 'delete'), $args
        );
    }

    public static function deleteById($id) {
        return static::getClient()->deleteById(static::getTableName(), $id);
    }

    public static function deleteByColumns($columns) {
        return static::getClient()->deleteByColumns(
            static::getTableName(), $columns
        );
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
            $position = strrpos($name, '\\');
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
