<?php
namespace Hyperframework\Db;

use ArrayAccess;
use Exception;

abstract class DbActiveRecord implements ArrayAccess {
    private static $tableNames = array();
    private $row;

    public function __construct(array $row = array()) {
        $this->setRow($row);
    }

    public function save() {
        return DbClient::save(static::getTableName(), $this->row);
    }

    public function delete() {
        return DbClient::deleteById(static::getTableName(), $this->row['id']);
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new Exception;
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

    public function mergeRow(array $value) {
        $this->row = $value + $this->row;
    }

    public static function findById($id) {
        $row = DbClient::findById(static::getTableName(), $id);
        if ($row === false) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function find($arg/*, ...*/) {
        $row = null;
        if (is_array($arg)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $arg);
        } else {
            $args = func_get_args();
            $sql = array_shift($args);
            $row = DbClient::findRow(self::completeSelectSql($sql), $args);
        }
        if ($row === false) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function findAll($arg/*, ...*/) {
        $result = array();
        $class = get_called_class();
        if (is_array($arg)) {
            $rows = DbClient::findAllByColumns(
                static::getTableName(), $columns
            );
        } else {
            $args = func_get_args();
            $sql = array_shift($args);
            $rows = DbClient::findAll(self::completeSelectSql($sql), $args);
        }
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
            if (strtoupper(substr($sql, 0, 6)) === 'SELECT'
                && ctype_alnum($sql[6]) === false
            ) {
                return $sql;
            }
        }
        return 'SELECT * FROM '
            . DbClient::quoteIdentifier(static::getTableName()) . ' ';
    }
}
