<?php
namespace Hyperframework\Db;

use PDO;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\InvalidOperationException;

class DbClientEngine {
    private $connection;
    private $connectionFactory;
    private $connectionPool = [];
    private $isConnectionPoolEnabled;

    public function findColumn($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetchColumn();
    }

    public function findColumnByColumns(
        $table, $columnName, array $columns
    ) {
        $result = $this->findByColumns(
            $table, $columns, [$columnName]
        );
        return $result->fetchColumn();
    }

    public function findColumnById($table, $columnName, $id) {
        $result = $this->findByColumns(
            $table, ['id' => $id], [$columnName]
        );
        return $result->fetchColumn();
    }

    public function findRow($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findRowByColumns(
        $table, array $columns, array $select = null
    ) {
        $result = $this->findByColumns(
            $table, $columns, $select
        );
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findRowById($table, $id, array $select = null) {
        $result = $this->findByColumns($table, ['id' => $id], $select);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByColumns(
        $table, array $columns, array $select = null
    ) {
        $result = $this->findByColumns($table, $columns, $select);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($sql, array $params = null) {
        return $this->sendSql($sql, $params, true);
    }

    public function findByColumns(
        $table, array $columns, array $select = null
    ) {
        if ($select === null) {
            $select = '*';
        } else {
            if (count($select) === 0) {
                $select = '*';
            } else {
                foreach ($select as &$name) {
                    $name = $this->quoteIdentifier($name);
                }
                $select = implode(', ', $select);
            }
        }
        list($where, $params) = $this->buildWhereByColumns($columns);
        $sql = 'SELECT ' . $select . ' FROM '
            . $this->quoteIdentifier($table);
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        return $this->find($sql, $params);
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
        if ($columnCount > 0) {
            $placeHolders = str_repeat('?, ', $columnCount - 1) . '?';
        } else {
            $placeHolders = '';
        }
        $sql = 'INSERT INTO ' . $this->quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES(' . $placeHolders . ')';
        $this->execute($sql, array_values($row));
    }

    public function update(
        $table, array $columns, $where, array $params = null
    ) {
        if (count($columns) === 0) {
            throw new InvalidArgumentException(
                "Arguemnt 'columns' cannot be an empty array."
            );
        }
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
        return $this->execute($sql, $params);
    }

    public function updateById($table, array $columns, $id) {
        return $this->update($table, $columns, 'id = ?', [$id]) > 0;
    }

    public function delete($table, $where, array $params = null) {
        if (is_array($where)) {
            list($where, $params) = $this->buildWhereByColumns($where);
        }
        if ($where !== null) {
            $where = ' WHERE ' . $where;
        }
        $sql = 'DELETE FROM ' . $this->quoteIdentifier($table) . $where;
        return $this->execute($sql, $params);
    }

    public function deleteById($table, $id) {
        return $this->delete($table, 'id = ?', [$id]) > 0;
    }

    public function execute($sql, array $params = null) {
        return $this->sendSql($sql, $params);
    }

    public function getLastInsertId() {
        return $this->getConnection()->lastInsertId();
    }

    public function beginTransaction() {
        $this->getConnection()->beginTransaction();
    }

    public function commit() {
        $this->getConnection()->commit();
    }

    public function rollback() {
        $this->getConnection()->rollBack();
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

    public function connect($name) {
        if ($this->isConnectionPoolEnabled()) {
            if (isset($this->connectionPool[$name])) {
                $this->connection = $this->connectionPool[$name];
            } else {
                $factory = $this->getConnectionFactory();
                $this->connection = $factory->createConnection($name);
                $this->connectionPool[$name] = $this->connection;
            }
        } else {
            $factory = $this->getConnectionFactory();
            $this->connection = $factory->createConnection($name);
        }
    }

    public function closeConnection($name = null) {
        if ($name === null) {
            if ($this->connection === null) {
                throw new InvalidOperationException(
                    'The current database connection equals null.'
                );
            }
            if ($this->isConnectionPoolEnabled() === false) {
                $this->connection = null;
                return;
            }
            $name = $this->connection->getName();
            $this->connection = null;
        } elseif ($this->connection !== null) {
            if ($this->connection->getName() === $name) {
                $this->connection = null;
                if ($this->isConnectionPoolEnabled() === false) {
                    return;
                }
            }
        }
        if (isset($this->connectionPool[$name]) === false) {
            throw new InvalidOperationException(
                "Database connection '$name' does not exist."
            );
        }
        unset($this->connectionPool[$name]);
    }

    public function setConnection($connection) {
        if ($connection === null
            || $this->isConnectionPoolEnabled() === false
        ) {
            $this->connection = $connection;
        } else {
            $this->connection = $connection;
            $connectionName = $connection->getName();
            $this->connectionPool[$connectionName] = $connection;
        }
    }

    public function getConnection($shouldConnect = true) {
        if ($this->connection === null && $shouldConnect) {
            $this->connect('default');
        }
        return $this->connection;
    }

    private function sendSql($sql, array $params = null, $isQuery = false) {
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

    private function isConnectionPoolEnabled() {
        if ($this->isConnectionPoolEnabled === null) {
            $this->isConnectionPoolEnabled = Config::getBoolean(
                'hyperframework.db.enable_connection_pool', true
            );
        }
        return $this->isConnectionPoolEnabled;
    }

    private function getConnectionFactory() {
        if ($this->connectionFactory === null) {
            $configName = 'hyperframework.db.connection_factory_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->connectionFactory = new DbConnectionFactory;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not"
                            . " exist, set using config '$configName'."
                    );
                }
                $this->connectionFactory = new $class;
            }
        }
        return $this->connectionFactory;
    }
}
