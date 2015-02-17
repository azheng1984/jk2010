<?php
namespace Hyperframework\Db;

use ArrayAccess;
use InvalidArgumentException;
use Hyperframework\Common\InvalidOperationException;

abstract class DbActiveRecord implements ArrayAccess {
    private static $tableNames = [];
    private $row;

    public function __construct(array $row = []) {
        $this->row = $row;
    }

    public function save() {
        $table = $this->getTableName();
        if (isset($this->row['id'])) {
            $id = $this->row['id'];
            unset($this->row['id']);
            if (count($this->row) === 0) {
                throw new DbActiveRecordException(
                    "Cannot update empty active record '"
                        . get_called_class(). "' where id equals to $id."
                );
            } else {
                if (DbClient::updateById($table, $this->row, $id) === false) {
                    $class = get_called_class();
                    throw new DbActiveRecordException(
                        "Failed to update active record '" . get_called_class()
                            . "' where id is equals to $id, "
                            . "because data does not exist in database."
                    );
                }
            }
            $this->row['id'] = $id;
            return;
        }
        $this->insert($table, $row);
        if (isset($this->row['id']) === false) {
            $this->row['id'] = $this->getLastInsertId();
        }
    }

    public function insert() {
        $this->insert($this->getTableName(), $this->row);
        if (isset($this->row['id']) === false) {
            $this->row['id'] = $this->getLastInsertId();
        }
    }

    public function update() {
        if (isset($this->row['id'])) {
            $id = $this->row['id'];
            unset($this->row['id']);
            if (count($this->row) === 0) {
                throw new DbActiveRecordException(
                    "Cannot update active record '"
                        . get_called_class(). "' where id equals to $id, "
                        . "because it only have id data field."
                );
            } else {
                return DbClient::updateById($table, $this->row, $id);
            }
            $this->row['id'] = $id;
            return;
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot update active record '$class' without an id data field."
            );
        }
    }

    public function delete() {
        if (isset($this->row['id'])) {
            DbClient::deleteById(static::getTableName(), $this->row['id']);
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot delete active record '$class' without an id data field."
            );
        }
    }

    public function offsetSet($offset, $value) {
        if ($offset === null) {
            throw new InvalidArgumentException('Null offset is invalid.');
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
        if (isset($this->row[$offset]) === false) {
            throw new DbActiveRecordException(
                "Data field '$offset' does not exist."
            );
        }
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

    public static function find($where/*, ...*/) {
        if ($where === null) {
            $where = [];
        }
        if (is_array($where)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $where);
            if ($row === false) {
                return;
            }
            return new static($row);
        }
        $class = get_called_class();
        $args = func_get_args();
        DbClient::find();
        $args[0] = DbClient::findBySql();
        return call_user_func_array($class . '::findBySql', $args);
    }

    public static function findById($id) {
        $row = DbClient::findRowById(static::getTableName(), $id);
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    public static function findBySql($sql/*, ...*/) {
        $args = func_get_args();
        if (isset($args[1]) && is_array($args[1])) {
            $args = $args[1];
        } else {
            $args = func_get_args();
            array_shift($args);
        }
        $row = DbClient::findRow(self::completeSelectSql($sql), $args);
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    public static function findAll($where = null/*, ...*/) {
        if ($where === null) {
            $where = [];
        }
        if (is_array($where)) {
            $rows = DbClient::findAllByColumns(static::getTableName(), $where);
            $result = [];
            foreach ($rows as $row) {
                $result[] = new static($row);
            }
            return $result;
        }
        $class = get_called_class();
        $args = func_get_args();
        $args[0] = 'WHERE ' . $args[0];
        return call_user_func_array($class . '::findAllBySql', $args);
    }

    public static function findAllBySql($sql/*, ...*/) {
        $args = func_get_args();
        array_shift($args);
        $rows = DbClient::findAll($sql, $args);
        $result = [];
        foreach ($rows as $row) {
            $result[] = new static($row);
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

    private static function completeSelectSql($where) {
        return 'SELECT * FROM '
            . DbClient::quoteIdentifier(static::getTableName()) . ' ' . $where;
    }
}
