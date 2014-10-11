<?php
namespace Hyperframework\Db;

use PDO;
use Exception;

class DbClientEngine {
    public function getById($table, $id, $columnNameOrNames = null) {
        $sql = 'SELECT * FROM '
            . $this->quoteIdentifier($table) . ' WHERE id = ?';
        return $this->getRow($sql, array($id));
    }

    public function getColumn($sql, array $params = null) {
        return $this->query($sql, $params)->fetchColumn();
    }

    public function getColumnByColumns($table, array $columns, $columnName) {
        $result = $this->queryByColumns($table, $columns, $columnName);
        return $result->fetchColumn();
    }

    public function getRow($sql, array $params = null) {
        return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public function getRowByColumns($table, array $columns, $selector = '*') {
        $result = $this->queryByColumns($table, $columns, $selector);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll($sql, array $params = null) {
        return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByColumns($table, array $columns, $selector = '*') {
        $result = $this->queryByColumns($table, $columns, $selector);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($table) {
        return $this->calculate($table, '*', 'COUNT');
    }

    public function min($table, $columnName) {
        return $this->calculate($table, $columnName, 'MIN');
    }

    public function max($table, $columnName) {
        return $this->calculate($table, $columnName, 'MAX');
    }

    public function sum($table, $columnName) {
        return $this->calculate($table, $columnName, 'SUM');
    }

    public function average($table, $columnName) {
        return $this->calculate($table, $columnName, 'AVG');
    }

    public function insert($table, array $row) {
        $keys = array();
        foreach (array_keys($row) as $key) {
            $keys[] = $this->quoteIdentifier($key);
        }
        $columnCount = count($row);
        if ($columnCount === 0) {
            throw new Exception;
        }
        $placeHolders = str_repeat('?, ', $columnCount - 1) . '?';
        $sql = 'INSERT INTO ' . $this->quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES(' . $placeHolders . ')';
        return $this->sendSql($sql, array_values($row));
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
            $tmp .= $this->quoteIdentifier($key) . ' = ?';
        }
        $sql = 'UPDATE ' . $this->quoteIdentifier($table)
            . ' SET ' . $tmp . $where;
        return $this->sendSql($sql, $params);
    }

    public function updateByColumns(
        $table, array $replacementColumns, array $filterColumns
    ) {
        list($where, $params) = $this->buildWhereByColumns($filterColumns);
        return call_user_func_array(
            '$this->update',
            array_merge(array($table, $replacementColumns, $where), $params)
        );
    }

    public function delete($table, $where, array $params = null) {
        if ($where !== null) {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($table) . $where;
        return $this->sendSql($sql, $params);
    }

    public function deleteByColumns($table, array $columns) {
        list($where, $params) = $this->buildWhereByColumns($columns);
        return $this->sendSql(
            'DELETE FROM ' . $this->quoteIdentifier($table)
                . ' WHERE ' . $where, $params
        );
    }

    public function deleteById($table, $id) {
        return $this->delete($table, 'id = ?', $id);
    }

    public function save($table, array &$row) {
        if (isset($row['id'])) {
            $id = $row['id'];
            unset($row['id']);
            $result = $this->update($table, $row, 'id = ?', $id);
            $row['id'] = $id;
            return $result;
        }
        $this->insert($table, $row);
        $row['id'] = $this->getLastInsertId();
        return 1;
    }

    public function execute($sql, array $params = null) {
        return $this->sendSql($sql, $params);
    }
 
    public function getLastInsertId() {
        return $this->getConnection()->lastInsertId();
    }

    public function beginTransaction() {
        return $this->getConnection()->beginTransaction();
    }

    public function commit() {
        return $this->getConnection()->commit();
    }

    public function rollback() {
        return $this->getConnection()->rollBack();
    }

    public function inTransaction() {
        return $this->getConnection()->inTransaction();
    }

    public function quoteIdentifier($identifier) {
        return $this->getConnection()->quoteIdentifier($identifier);
    }

    public function getConnection() {
        return DbContext::getConnection();
    }

    public function prepare($sql, array $driverOptions = array()) {
        return $this->getConnection()->prepare($sql, $driverOptions);
    }

    protected function sendSql($sql, array $params = null, $isQuery = false) {
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
        $table = $this->quoteIdentifier($table);
        if ($columnName !== '*') {
            $columnName = $this->quoteIdentifier($columnName);
        }
        return $this->getColumn(
            'SELECT ' . $function . '(' . $columnName . ') FROM ' . $table
        );
    }

    private function query($sql, array $params = null) {
        return $this->sendSql($sql, $params, true);
    }

    private function queryByColumns($table, array $columns, $selector) {
        list($where, $params) = $this->buildWhereByColumns($columns);
        $sql = 'SELECT ' . $selector . ' FROM ' . $this->quoteIdentifier($table);
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
            $where .= $this->quoteIdentifier($key) . ' = ?';
        }
        if ($where === null) {
            throw new Exception;
        }
        return array($where, $params);
    }
}
