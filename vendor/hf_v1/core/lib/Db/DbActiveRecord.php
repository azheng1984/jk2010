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

    public function setRow(array $row) {
        $this->row = $row;
    }

    public function mergeRow(array $row) {
        $this->row = $row + $this->row;
    }

    public static function find($mixed/*, ...*/) {
        if ($mixed === null) {
            $mixed = array();
        }
        $class = get_called_class();
        if (is_array($mixed)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $mixed);
            if ($row === false) {
                return;
            }
            return new $class($row);
        }
        $args = func_get_args();
        $args[0] = 'WHERE ' . $args[0];
        return call_user_func_array($class . '::findBySql', $args);
    }

    public static function findById($id) {
        $row = DbClient::findById(static::getTableName(), $id);
        if ($row === false) {
            return;
        }
        $class = get_called_class();
        return new $class($row);
    }

    public static function findBySql($sql/*, ...*/) {
        if (isset($args[1]) && is_array($args[1])) {
            $args = $args[1];
        } else {
            $args = func_get_args();
            array_shift($args);
        }
        $class = get_called_class();
        $row = DbClient::findRow(self::completeSelectSql($sql), $args);
        if ($row === false) {
            return;
        }
        return new $class($row);
    }

    public static function findAll($mixed = null/*, ...*/) {
        if ($mixed === null) {
            $mixed = array();
        }
        $class = get_called_class();
        if (is_array($mixed)) {
            $rows = DbClient::findAllByColumns(static::getTableName(), $mixed);
            $result = array();
            foreach ($rows as $row) {
                $result[] = new $class($row);
            }
            return $result;
        }
        $args = func_get_args();
        $args[0] = 'WHERE ' . $args[0];
        return call_user_func_array($class . '::findAllBySql', $args);
    }

    public static function findAllBySql($sql/*, ...*/) {
        if (isset($args[1]) && is_array($args[1])) {
            $args = $args[1];
        } else {
            $args = func_get_args();
            array_shift($args);
        }
        $rows = DbClient::findAll(self::completeSelectSql($sql), $args);
        $result = array();
        $class = get_called_class();
        foreach ($rows as $row) {
            $result[] = new $class($row);
        }
        return $result;
    }

    public static function count($where = null/*, ...*/) {
        return DbClient::count(
            static::getTableName(), $where, array_slice(func_get_args(), 1)
        );
    }

    public static function min($columnName, $where = null/*, ...*/) {
        return DbClient::min(
            static::getTableName(),
            $columnName,
            $where,
            array_slice(func_get_args(), 2)
        );
    }

    public static function max($columnName, $where = null/*, ...*/) {
        return DbClient::max(
            static::getTableName(),
            $columnName,
            $where,
            array_slice(func_get_args(), 2)
        );
    }

    public static function sum($columnName, $where = null/*, ...*/) {
        return DbClient::sum(
            static::getTableName(),
            $columnName,
            $where,
            array_slice(func_get_args(), 2)
        );
    }

    public static function average($columnName, $where = null/*, ...*/) {
        return DbClient::average(
            static::getTableName(),
            $columnName,
            $where,
            array_slice(func_get_args(), 2)
        );
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
