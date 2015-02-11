<?php
namespace Hyperframework\Db;

use PDO;
use LogicException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbClientEngine {
    private $connection;
    private $connectionFactory;

    public function findById($table, $id, $selectedColumnNameOrNames = null) {
        if ($selectedColumnNameOrNames !== null
            && is_array($selectedColumnNameOrNames) === false
        ) {
            $selectedColumnNameOrNames = [$selectedColumnNameOrNames];
        }
        $result = $this->queryByColumns(
            $table, ['id' => $id], $selectedColumnNameOrNames
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findColumn($sql, array $params = null) {
        $result = $this->query($sql, $params);
        return $result->fetchColumn();
    }

    public function findColumnByColumns(
        $table, array $columns, $selectedColumnName
    ) {
        $result = $this->queryByColumns(
            $table, $columns, [$selectedColumnName]
        );
        return $result->fetchColumn();
    }

    public function findRow($sql, array $params = null) {
        $result = $this->query($sql, $params);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findRowByColumns(
        $table, array $columns, array $selectedColumnNames = null
    ) {
        $result = $this->queryByColumns(
            $table, $columns, $selectedColumnNames
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll($sql, array $params = null) {
        $result = $this->query($sql, $params);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByColumns(
        $table, array $columns, $selectedColumnNameOrNames = null
    ) {
        if (is_array($selectedColumnNameOrNames) === false) {
            $selectedColumnNameOrNames = [$selectedColumnNameOrNames];
        }
        $result = $this->queryByColumns(
            $table, $columns, $selectedColumnNameOrNames
        );
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count($table, $where = null, array $params = null) {
        return $this->calculate($table, '*', 'COUNT', $where, $params);
    }

    public function min(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'MIN', $where, $params);
    }

    public function max(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'MAX', $where, $params);
    }

    public function sum(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'SUM', $where, $params);
    }

    public function average(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'AVG', $where, $params);
    }

    public function insert($table, array $row) {
        $keys = [];
        foreach (array_keys($row) as $key) {
            $keys[] = $this->quoteIdentifier($key);
        }
        $columnCount = count($row);
        if ($columnCount === 0) {
            return;
        }
        $placeHolders = str_repeat('?, ', $columnCount - 1) . '?';
        $sql = 'INSERT INTO ' . $this->quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES(' . $placeHolders . ')';
        $this->sendSql($sql, array_values($row));
    }

    public function update(
        $table, array $columns, $where, array $params = null
    ) {
        if (is_array($where)) {
            list($where, $params) = $this->buildWhereByColumns($where);
        }
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

    public function delete($table, $where, array $params = null) {
        if (is_array($where)) {
            list($where, $params) = $this->buildWhereByColumns($where);
        }
        if ($where !== null) {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($table) . $where;
        return $this->sendSql($sql, $params);
    }

    public function deleteById($table, $id) {
        return $this->delete($table, 'id = ?', $id);
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

    public function prepare($sql, array $driverOptions = []) {
        return $this->getConnection()->prepare($sql, $driverOptions);
    }

    public function connect($name = 'default') {
        $factory = $this->getConnectionFactory();
        $this->connection = $factory->create($name);
    }

    public function setConnection($value) {
        $this->connection = $value;
    }

    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    protected function sendSql($sql, array $params = null, $isQuery = false) {
        $connection = $this->getConnection();
        if ($params === null || count($params) === 0) {
            return $isQuery ?
                $connection->query($sql) : $connection->exec($sql);
        }
        $statement = $connection->prepare($sql);
        $statement->execute($params);
        if ($isQuery) {
            return $statement;
        }
        return $statement->rowCount();
    }

    private function calculate(
        $table, $columnName, $function, $where, array $params = null
    ) {
        $table = $this->quoteIdentifier($table);
        if ($columnName !== '*') {
            $columnName = $this->quoteIdentifier($columnName);
        }
        if (is_array($where)) {
            list($where, $params) = $this->buildWhereByColumns($where);
        }
        $sql = 'SELECT ' . $function . '(' . $columnName . ') FROM ' . $table;
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        return $this->findColumn($sql, $params);
    }

    private function query($sql, array $params = null) {
        return $this->sendSql($sql, $params, true);
    }

    private function queryByColumns(
        $table, array $columns, array $selectedColumnNames = null
    ) {
        $selector = null;
        if ($selectedColumnNames === null) {
            $selector = '*';
        } else {
            if (count($selectedColumnNames) === 0) {
                $selector = '*';
            } else {
                foreach ($selectedColumnNames as &$name) {
                    $name = $this->quoteIdentifier($name);
                }
                $selector = implode(', ', $selectedColumnNames);
            }
        }
        list($where, $params) = $this->buildWhereByColumns($columns);
        $sql = 'SELECT ' . $selector . ' FROM '
            . $this->quoteIdentifier($table);
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        return $this->sendSql($sql, $params, true);
    }

    private function buildWhereByColumns(array $columns) {
        $params = [];
        $where = null;
        foreach ($columns as $key => $value) {
            $params[] = $value;
            if ($where !== null) {
                $where = ' AND ';
            }
            $where .= $this->quoteIdentifier($key) . ' = ?';
        }
        return [$where, $params];
    }

    private function getConnectionFactory() {
        if ($this->connectionFactory === null) {
            $configName = 'hyperframework.db.connection.factory_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->connectionFactory = new DbConnectionFactory;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Database connection factory Class '$class' does not"
                            . " exist, defined in config '$configName'."
                    );
                }
                $this->connectionFactory = new $class;
            }
        }
        return $this->connectionFactory;
    }
}
