<?php
namespace Hyperframework\Db;

abstract class DbTable {
    private static $instances = array();
    private $name;

    public static function getById($id, array $columnNameOrNames = null) {
        return DbClient::getById(
            static::getTableName(), $id, $columnNameOrNames
        );
    }

    public static function getColumnByColumns($columns, $columnName) {
        return DbClient::getColumnByColumns(
            static::getTableName(), $columns, $columnName
        );
    }

    public static function getRowByColumns(
        $columns, array $columnNames = null
    ) {
        return DbClient::getRowByColumns(
            static::getTableName(), $columns, $columnNames
        );
    }

    public static function getAllByColumns(
        $columns, array $columnNames = null
    ) {
        return DbClient::getAllByColumns(
            static::getTableName(), $columns, $columnNames
        );
    }

    public static function count() {
        return DbClient::count(static::getTableName());
    }

    public static function min($columnName) {
        return DbClient::min(static::getTableName(), $columnName);
    }

    public static function max($columnName) {
        return DbClient::max(static::getTableName(), $columnName);
    }

    public static function sum($columnName) {
        return DbClient::sum(static::getTableName(), $columnName);
    }

    public static function average($columnName) {
        return DbClient::average(static::getTableName(), $columnName);
    }

    public static function save(array &$row) {
        return DbClient::save(static::getTableName(), $row);
    }

    public static function delete($id) {
        return DbClient::deleteById(static::getTableName(), $id);
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
