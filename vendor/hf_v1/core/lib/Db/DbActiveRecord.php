<?php
namespace Hyperframework\Db;

abstract class DbActiveRecord implements ArrayAccess, Iterator {
    private static $tableNames = array();
    private $row;

    public function __construct(array $row = null) {
        $this->$row = $row;
    }

    public static function getById($id) {
        $row = DbClient::getById(static::getTableName(), $id);
        if ($row === null) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function getByColumns($columns) {
        $row = DbClient::getRowByColumns(static::getTableName(), $columns);
        if ($row === null) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function getAllByColumns($columns) {
        $rows = DbClient::getAllByColumns(static::getTableName(), $columns);
        $result = array();
        $class = get_called_class();
        foreach ($rows as $row) {
            $result[] = new $class($row);
        }
        return $result;
    }

    public static function getBySql($sql) {
        $row = DbClient::getRow($sql);
        if ($row === null) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function getAllBySql($sql) {
        $rows = DbClient::getAll($sql);
        $result = array();
        $class = get_called_class();
        foreach ($rows as $row) {
            $result[] = new $class($row);
        }
        return $result;
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

    public function save() {
        return DbClient::save(static::getTableName(), $this->row);
    }

    public function delete() {
        return DbClient::deleteById(static::getTableName(), $this->row['id']);
    }

    protected static function getTableName() {
        $class = get_called_class();
        if (isset(self::$tableNames[$class]) === false) {
            $position = strrpos($class, '\\');
            if ($position !== false) {
                self::$tableNames[$class] = substr($class, $position + 1);
            }
        }
        return self::$tableNames[$class];
    }

    public function getRow() {
        return $this->row;
    }

    public function setRow(array $value) {
        $this->row = $value;
    }
}
