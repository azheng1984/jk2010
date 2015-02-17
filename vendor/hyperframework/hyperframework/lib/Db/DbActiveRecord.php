<?php
namespace Hyperframework\Db;

use ArrayAccess;
use InvalidArgumentException;

abstract class DbActiveRecord implements ArrayAccess {
    private static $tableNames = [];
    private $row;

    public function __construct(array $row = []) {
        $this->row = $row;
    }

    public function insert() {
        DbClient::insert($this->getTableName(), $this->row);
        if (isset($this->row['id']) === false) {
            $this->row['id'] = DbClient::getLastInsertId();
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
                        . "because it only has an id column."
                );
            } else {
                $this->row['id'] = $id;
                return DbClient::updateById($table, $this->row, $id);
            }
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot update active record '$class' without an id column."
            );
        }
    }

    public function delete() {
        if (isset($this->row['id'])) {
            DbClient::deleteById(static::getTableName(), $this->row['id']);
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot delete active record '$class' without an id column."
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
                "Column '$offset' does not exist."
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

    public static function find($where/*, ...*/) {
        if ($where === null) {
            $where = [];
        }
        if (is_array($where)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $where);
        } elseif (is_string($where)) {
            $rows = DbClient::findRow(
                self::completeSelectSql($where),
                self::getParams(func_get_args(), 1)
            );
        } else {
            $type = gettype($where);
            if ($type === 'object') {
                $type = get_class($where);
            }
            throw InvalidArgumentException(
                "Arguemnt 'where' must be a string or an array, $type given."
            );
        }
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    public static function findById($id) {
        $row = DbClient::findRowById(static::getTableName(), $id);
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    public static function findBySql($sql/*, ...*/) {
        $row = DbClient::findRow($sql, self::getParams(func_get_args(), 1));
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
        } elseif (is_string($where)) {
            $rows = DbClient::findAll(
                self::completeSelectSql($where),
                self::getParams(func_get_args(), 1)
            );
        } else {
            $type = gettype($where);
            if ($type === 'object') {
                $type = get_class($where);
            }
            throw InvalidArgumentException(
                "Arguemnt 'where' must be a string or an array, $type given."
            );
        }
        $result = [];
        foreach ($rows as $row) {
            $result[] = new static($row);
        }
        return $result;
    }

    public static function findAllBySql($sql/*, ...*/) {
        $rows = DbClient::findAll($sql, self::getParams(func_get_args(), 1));
        $result = [];
        foreach ($rows as $row) {
            $result[] = new static($row);
        }
        return $result;
    }

    public static function count($where = null/*, ...*/) {
        return DbClient::count(
            static::getTableName(), $where, self::getParams(func_get_args())
        );
    }

    public static function min($columnName, $where = null/*, ...*/) {
        return DbClient::min(
            static::getTableName(),
            $columnName,
            $where,
            self::getParams(func_get_args(), 2)
        );
    }

    public static function max($columnName, $where = null/*, ...*/) {
        return DbClient::max(
            static::getTableName(),
            $columnName,
            $where,
            self::getParams(func_get_args(), 2)
        );
    }

    public static function sum($columnName, $where = null/*, ...*/) {
        return DbClient::sum(
            static::getTableName(),
            $columnName,
            $where,
            self::getParams(func_get_args(), 2)
        );
    }

    public static function average($columnName, $where = null/*, ...*/) {
        return DbClient::average(
            static::getTableName(),
            $columnName,
            $where,
            self::getParams(func_get_args(), 2)
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
        $result = 'SELECT * FROM '
            . DbClient::quoteIdentifier(static::getTableName());
        $where = (string)$where;
        if ($where !== '') {
            $result .= ' WHERE ' . $where;
        }
        return $where;
    }

    private static function getParams(array $args, $offset = 1) {
        if (isset($args[$offset]) === false) {
            return [];
        }
        if (is_array($args[$offset])) {
            return $args[$offset];
        }
        return array_slice($args, $offset);
    }
}
