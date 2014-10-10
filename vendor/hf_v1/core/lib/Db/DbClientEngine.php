<?php
namespace Hyperframework\Db;

use PDO;
use Exception;

class DbClientEngine {
    public function getById($table, $id, $columnNameOrNames = null) {
        $sql = 'SELECT * FROM '
            . self::quoteIdentifier($table) . ' WHERE id = ?';
        return static::getRow($sql, $id);
    }

    public function getColumn($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchColumn();
    }

    public function getColumnByColumns($table, $columns, $columnName) {
        $result = self::queryByColumns($table, $columns, $columnName);
        return $result->fetchColumn();
    }

    public function getRow($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public function getRowByColumns($table, $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByColumns($table, $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($table) {
        return self::calculate($table, '*', 'COUNT');
    }

    public function min($table, $columnName) {
        return self::calculate($table, $columnName, 'MIN');
    }

    public function max($table, $columnName) {
        return self::calculate($table, $columnName, 'MAX');
    }

    public function sum($table, $columnName) {
        return self::calculate($table, $columnName, 'SUM');
    }

    public function average($table, $columnName) {
        return self::calculate($table, $columnName, 'AVG');
    }

    public function insert($table, $row) {
        $keys = array();
        foreach (array_keys($row) as $key) {
            $keys[] = self::quoteIdentifier($key);
        }
        $columnCount = count($row);
        if ($columnCount === 0) {
            throw new Exception;
        }
        $placeHolders = str_repeat('?, ', $columnCount - 1) . '?';
        $sql = 'INSERT INTO ' . self::quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES(' . $placeHolders . ')';
        return static::sendSql($sql, array_values($row));
    }

    public function update($table, $columns, $where/*, $mixed, ...*/) {
        $params = array_values($columns);
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_merge(
                $params, array_slice(func_get_args(), 3)
            );
        }
        $tmp = null;
        foreach (array_keys($columns) as $key) {
            $tmp .= self::quoteIdentifier($key) . ' = ?';
        }
        $sql = 'UPDATE ' . self::quoteIdentifier($table)
            . ' SET ' . $tmp . $where;
        return static::sendSql($sql, $params);
    }

    public function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
        list($where, $params) = self::buildWhereByColumns($filterColumns);
        return call_user_func_array(
            'static::update',
            array_merge(array($table, $replacementColumns, $where), $params)
        );
    }

    public function delete($table, $where/*, $mixed, ...*/) {
        $params = array();
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_slice(func_get_args(), 2);
        }
        $sql = 'DELETE FROM ' . self::quoteIdentifier($table) . $where;
        return static::sendSql($sql, $params);
    }

    public function deleteByColumns($table, $columns) {
        list($where, $params) = self::buildWhereByColumns($columns);
        return static::sendSql(
            'DELETE FROM ' . self::quoteIdentifier($table)
                . ' WHERE ' . $where, $params
        );
    }

    public function deleteById($table, $id) {
        return static::delete($table, 'id = ?', $id);
    }

    public function save($table, array &$row) {
        if (isset($row['id'])) {
            $id = $row['id'];
            unset($row['id']);
            $result = static::update($table, $row, 'id = ?', $id);
            $row['id'] = $id;
            return $result;
        }
        static::insert($table, $row);
        $row['id'] = static::getLastInsertId();
        return 1;
    }

    public function execute($sql/*, $mixed, ...*/) {
        $params = func_get_args();
        $sql = array_shift($params);
        return static::sendSql($sql, $params);
    }
 
    public function getLastInsertId() {
        return static::getConnection()->lastInsertId();
    }

    public function beginTransaction() {
        return static::getConnection()->beginTransaction();
    }

    public function commit() {
        return static::getConnection()->commit();
    }

    public function rollback() {
        return static::getConnection()->rollBack();
    }

    public function inTransaction() {
        return static::getConnection()->inTransaction();
    }

    public function quoteIdentifier($identifier) {
        return static::getConnection()->quoteIdentifier($identifier);
    }

    public function prepare($sql, $driverOptions = array()) {
        return $this->getConnection()->prepare($sql, $driverOptions);
    }

    public function sendSql($sql, $params, $isQuery = false) {
        $connection = $this->getConnection();
        if ($params === null || count($params) === 0) {
            return $isQuery ?
                $connection->query($sql) : $connection->exec($sql);
        }
        if (is_array($params[0])) {
            $params = $params[0];
        }
        $statement = $connection->prepare($sql);
        $statement->execute($params);
        if ($isQuery) {
            return $statement;
        }
        return $statement->rowCount();
    }

    public function getConnection() {
        return DbContext::getConnection();
    }

    private function calculate($table, $columnName, $function) {
        $table = self::quoteIdentifier($table);
        if ($columnName !== '*') {
            $columnName = self::quoteIdentifier($columnName);
        }
        return static::getColumn(
            'SELECT ' . $function . '(' . $columnName . ') FROM ' . $table
        );
    }

    private function query($params) {
        $sql = array_shift($params);
        return static::sendSql($sql, $params, true);
    }

    private function queryByColumns($table, $columns, $selector) {
        list($where, $params) = self::buildWhereByColumns($columns);
        $sql = 'SELECT ' . $selector . ' FROM ' . self::quoteIdentifier($table);
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        array_unshift($params, $sql);
        return self::query($params);
    }

    private function buildWhereByColumns($columns) {
        $params = array();
        $where = null;
        foreach ($columns as $key => $value) {
            $params[] = $value;
            if ($where !== null) {
                $where = ' AND ';
            }
            $where .= self::quoteIdentifier($key) . ' = ?';
        }
        if ($where === null) {
            throw new Exception;
        }
        return array($where, $params);
    }
}
