<?php
namespace Hyperframework\Db;

use ArrayAccess;

abstract class DbActiveRecord implements ArrayAccess {
    private static $tableNames = array();
    private $row;

    public function __construct(array $row = array()) {
        $this->row = $row;
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
        $result = array();
        $class = get_called_class();
        $rows = DbClient::getAllByColumns(static::getTableName(), $columns);
        foreach ($rows as $row) {
            $result[] = new $class($row);
        }
        return $result;
    }

    public static function getBySql($sql/*, ...*/) {
        $args = func_get_args();
        array_shift($args);
        $row = DbClient::getRow(self::completeSelectSql($sql), $args);
        if ($row === null) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function getAllBySql($sql/*, ...*/) {
        $result = array();
        $class = get_called_class();
        $args = func_get_args();
        array_shift($args);
        $rows = DbClient::getAll(self::completeSelectSql($sql), $args);
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

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->row[] = $value;
        } else {
            $this->row[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->row[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->row[$offset]);
    }

    public function offsetGet($offset) {
        return $this->row[$offset];
    }

    public function getRow() {
        return $this->row;
    }

    public function setRow(array $value) {
        $this->row = $value;
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

    private static function completeSelectSql($sql) {
        if (strlen($sql) > 6) {
            if (ctype_alnum($sql[6]) === false
                && strtoupper(substr($sql, 0, 6)) === 'SELECT'
            ) {
                return $sql;
            }
        }
        return 'SELECT * FROM '
            . DbClient::quoteIdentifier(static::getTableName()) . ' ';
    }
}
