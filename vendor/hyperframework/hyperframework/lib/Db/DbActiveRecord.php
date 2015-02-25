<?php
namespace Hyperframework\Db;

use InvalidArgumentException;

abstract class DbActiveRecord {
    private static $tableNames = [];
    private $row;

    public function __construct(array $row = []) {
        $this->setRow($row);
    }

    public static function find($where/*, ...*/) {
        if (is_array($where)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $where);
        } elseif (is_string($where) || $where === null) {
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
                "Argument 'where' must be a string or an array, $type given."
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
        if (is_array($where)) {
            $rows = DbClient::findAllByColumns(static::getTableName(), $where);
        } elseif (is_string($where) || $where === null) {
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
                "Argument 'where' must be a string or an array, $type given."
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

    public function insert() {
        DbClient::insert($this->getTableName(), $this->getRow());
        if ($this->hasColumn('id') === false) {
            $this->setColumn('id', DbClient::getLastInsertId());
        }
    }

    public function update() {
        $row = $this->getRow();
        if (isset($row['id'])) {
            $id = $row['id'];
            if (count($row) === 1) {
                throw new DbActiveRecordException(
                    "Cannot update active record '"
                        . get_called_class(). "' where id equals to $id, "
                        . "because it only has an id column."
                );
            } else {
                unset($row['id']);
                return DbClient::updateById(static::getTableName(), $row, $id);
            }
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot update active record '$class' which is not persistent, "
                    . "because column 'id' is missing."
            );
        }
    }

    public function delete() {
        if ($this->hasColumn('id')) {
            DbClient::deleteById(
                static::getTableName(), $this->getColumn('id')
            );
        } else {
            $class = get_called_class();
            throw new DbActiveRecordException(
                "Cannot delete active record '$class' which is not persistent, "
                    . "because column 'id' is missing."
            );
        }
    }

    protected function getRow() {
        return $this->row;
    }

    protected function setRow(array $row) {
        $this->row = $row;
    }

    protected function getColumn($name) {
        if (isset($this->row[$name])) {
            return $this->row[$name];
        }
    }

    protected function setColumn($name, $value) {
        $this->row[$name] = $value;
    }

    protected function hasColumn($name) {
        return isset($this->row[$name]);
    }

    protected function removeColumn($name) {
        unset($this->row[$name]);
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
        return $result;
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
