<?php
namespace Hyperframework\Db;

use PDO;
use Exception;

class DbClientEngine {
    public function getById($table, $id, $columnNameOrNames = null) {
        $sql = 'SELECT * FROM '
            . self::quoteIdentifier($table) . ' WHERE id = ?';
        return static::getRow($sql, array($id));
    }

    public function getColumn($sql, array $params = null) {
        return static::query($sql, $params)->fetchColumn();
    }

    public function getColumnByColumns($table, array $columns, $columnName) {
        $result = self::queryByColumns($table, $columns, $columnName);
        return $result->fetchColumn();
    }

    public function getRow($sql, array $params = null) {
        return static::query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function getRowByColumns($table, array $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($sql, array $params = null) {
        return static::query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByColumns($table, array $columns, $selector = '*') {
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

    public function insert($table, array $row) {
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

    public function update(
        $table, array $columns, $where, array $params = null
    ) {
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_merge(array_values($columns), $params);
        } else {
            $params = array_values($columns);
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
        $table, array $replacementColumns, array $filterColumns
    ) {
        list($where, $params) = self::buildWhereByColumns($filterColumns);
        return call_user_func_array(
            'static::update',
            array_merge(array($table, $replacementColumns, $where), $params)
        );
    }

    public function delete($table, $where, array $params = null) {
        if ($where !== null) {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM ' . self::quoteIdentifier($table) . $where;
        return static::sendSql($sql, $params);
    }

    public function deleteByColumns($table, array $columns) {
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

    public function execute($sql, array $params = null) {
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

    public function getConnection() {
        return DbContext::getConnection();
    }

    public function prepare($sql, array $driverOptions = array()) {
        return $this->getConnection()->prepare($sql, $driverOptions);
    }

    protected function sendSql($sql, array $params, $isQuery = false) {
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

    private function calculate($table, $columnName, $function) {
        $table = self::quoteIdentifier($table);
        if ($columnName !== '*') {
            $columnName = self::quoteIdentifier($columnName);
        }
        return $this->getColumn(
            'SELECT ' . $function . '(' . $columnName . ') FROM ' . $table
        );
    }

    private function query($sql, array $params) {
        return $this->sendSql($sql, $params, true);
    }

    private function queryByColumns($table, array $columns, $selector) {
        list($where, $params) = self::buildWhereByColumns($columns);
        $sql = 'SELECT ' . $selector . ' FROM ' . self::quoteIdentifier($table);
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        array_unshift($params, $sql);
        return $this->query($params);
    }

    private function buildWhereByColumns(array $columns) {
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
