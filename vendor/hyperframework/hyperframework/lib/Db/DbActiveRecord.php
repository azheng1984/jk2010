<?php
namespace Hyperframework\Db;

use InvalidArgumentException;

abstract class DbActiveRecord {
    private static $tableNames = [];
    private $row;

    /**
     * @param array $row
     */
    public function __construct(array $row = []) {
        $this->setRow($row);
    }

    /**
     * @param array|string $where
     * @param array $params
     * @return static
     */
    public static function find($where, array $params = null) {
        if (is_array($where)) {
            $row = DbClient::findRowByColumns(static::getTableName(), $where);
        } elseif (is_string($where) || $where === null) {
            $row = DbClient::findRow(
                self::completeSelectSql($where),
                $params
            );
        } else {
            $type = gettype($where);
            throw new InvalidArgumentException(
                "Argument 'where' must be a string or an array, $type given."
            );
        }
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    /**
     * @param int $id
     * @return static
     */
    public static function findById($id) {
        $row = DbClient::findRowById(static::getTableName(), $id);
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return static
     */
    public static function findBySql($sql, array $params = null) {
        $row = DbClient::findRow($sql, $params);
        if ($row === false) {
            return;
        }
        return new static($row);
    }

    /**
     * @param array|string $where
     * @param array $params
     * @return static
     */
    public static function findAll($where = null, array $params = null) {
        if (is_array($where)) {
            $rows = DbClient::findAllByColumns(static::getTableName(), $where);
        } elseif (is_string($where) || $where === null) {
            $rows = DbClient::findAll(
                self::completeSelectSql($where), $params
            );
        } else {
            $type = gettype($where);
            throw new InvalidArgumentException(
                "Argument 'where' must be a string or an array, $type given."
            );
        }
        $result = [];
        foreach ($rows as $row) {
            $result[] = new static($row);
        }
        return $result;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return static[]
     */
    public static function findAllBySql($sql, array $params = null) {
        $rows = DbClient::findAll($sql, $params);
        $result = [];
        foreach ($rows as $row) {
            $result[] = new static($row);
        }
        return $result;
    }

    /**
     * @param array|string $where
     * @param array $params
     * @return int
     */
    public static function count($where = null, array $params = null) {
        return DbClient::count(static::getTableName(), $where, $params);
    }

    /**
     * @param string $columnName
     * @param array|string $where
     * @param array $params
     * @return mixed
     */
    public static function min(
        $columnName, $where = null, array $params = null
    ) {
        return DbClient::min(
            static::getTableName(),
            $columnName,
            $where,
            $params
        );
    }

    /**
     * @param string $columnName
     * @param array|string $where
     * @param array $params
     * @return mixed
     */
    public static function max(
        $columnName, $where = null, array $params = null
    ) {
        return DbClient::max(
            static::getTableName(),
            $columnName,
            $where,
            $params
        );
    }

    /**
     * @param string $columnName
     * @param array|string $where
     * @param array $params
     * @return mixed
     */
    public static function sum(
        $columnName, $where = null, array $params = null
    ) {
        return DbClient::sum(
            static::getTableName(),
            $columnName,
            $where,
            $params
        );
    }

    /**
     * @param string $columnName
     * @param array|string $where
     * @param array $params
     * @return mixed
     */
    public static function average(
        $columnName, $where = null, array $params = null
    ) {
        return DbClient::average(
            static::getTableName(),
            $columnName,
            $where,
            $params
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

    /**
     * @return array
     */
    protected function getRow() {
        return $this->row;
    }

    /**
     * @param array $row
     */
    protected function setRow(array $row) {
        $this->row = $row;
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getColumn($name) {
        if (isset($this->row[$name])) {
            return $this->row[$name];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    protected function setColumn($name, $value) {
        $this->row[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasColumn($name) {
        return isset($this->row[$name]);
    }

    /**
     * @param string $name
     */
    protected function removeColumn($name) {
        unset($this->row[$name]);
    }

    /**
     * @return string
     */
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

    /**
     * @param string $where
     * @return string
     */
    private static function completeSelectSql($where) {
        $result = 'SELECT * FROM '
            . DbClient::quoteIdentifier(static::getTableName());
        $where = (string)$where;
        if ($where !== '') {
            $result .= ' WHERE ' . $where;
        }
        return $result;
    }
}
