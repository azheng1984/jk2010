<?php
namespace Hyperframework\Db;

use PDO;
use InvalidArgumentException;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbClientEngine {
    private $connection;
    private $connectionFactory;
    private $connectionPool = [];
    private $isConnectionPoolEnabled;

    /**
     * @param string $sql
     * @param arrary $params
     * @return mixed
     */
    public function findColumn($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetchColumn();
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param array $columns
     * @return mixed
     */
    public function findColumnByColumns($table, $columnName, array $columns) {
        $result = $this->findByColumns($table, $columns, [$columnName]);
        return $result->fetchColumn();
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param mixed $id
     * @return mixed
     */
    public function findColumnById($table, $columnName, $id) {
        $result = $this->findByColumns($table, ['id' => $id], [$columnName]);
        return $result->fetchColumn();
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function findRow($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return array
     */
    public function findRowByColumns(
        $table, array $columns, array $select = null
    ) {
        $result = $this->findByColumns($table, $columns, $select);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param mixed $id
     * @param array $select
     * @return array
     */
    public function findRowById($table, $id, array $select = null) {
        $result = $this->findByColumns($table, ['id' => $id], $select);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array[]
     */
    public function findAll($sql, array $params = null) {
        $result = $this->find($sql, $params);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return array[]
     */
    public function findAllByColumns(
        $table, array $columns, array $select = null
    ) {
        $result = $this->findByColumns($table, $columns, $select);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return DbStatement
     */
    public function find($sql, $params = null) {
        return $this->sendSql($sql, $params, true);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return DbStatement
     */
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

    /**
     * @param string $table
     * @param string|array $where
     * @param array $params
     * @return int
     */
    public function count($table, $where = null, array $params = null) {
        return (int)$this->calculate($table, '*', 'COUNT', $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public function min(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'MIN', $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public function max(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'MAX', $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public function sum(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'SUM', $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public function average(
        $table, $columnName, $where = null, array $params = null
    ) {
        return $this->calculate($table, $columnName, 'AVG', $where, $params);
    }

    /**
     * @param string $table
     * @param array $row
     */
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

    /**
     * @param string $table
     * @param array $columns
     * @param string|array $where
     * @param array $params
     */
    public function update($table, $columns, $where, array $params = null) {
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

    /**
     * @param string $table
     * @param array $columns
     * @param mixed $id
     */
    public function updateById($table, array $columns, $id) {
        return $this->update($table, $columns, 'id = ?', [$id]) > 0;
    }

    /**
     * @param string $table
     * @param string|array $where
     * @param array $params
     */
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

    /**
     * @param string $table
     * @param mixed $id
     */
    public function deleteById($table, $id) {
        return $this->delete($table, 'id = ?', [$id]) > 0;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return int
     */
    public function execute($sql, array $params = null) {
        return $this->sendSql($sql, $params);
    }

    /**
     * @return mixed
     */
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

    /**
     * @return bool
     */
    public function inTransaction() {
        return $this->getConnection()->inTransaction();
    }

    /**
     * @param string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier) {
        return $this->getConnection()->quoteIdentifier($identifier);
    }

    /**
     * @param string $sql
     * @param array $driverOptions
     * @return DbStatement
     */
    public function prepare($sql, $driverOptions = []) {
        return $this->getConnection()->prepare($sql, $driverOptions);
    }

    /**
     * @param string $name
     */
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

    /**
     * @param string $name
     */
    public function closeConnection($name = null) {
        if ($name === null) {
            if ($this->connection === null) {
                return;
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
            return;
        }
        unset($this->connectionPool[$name]);
    }

    /**
     * @param DbConnection $connection
     */
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

    /**
     * @param bool $shouldConnect
     * @return DbConnection
     */
    public function getConnection($shouldConnect = true) {
        if ($this->connection === null && $shouldConnect) {
            $this->connect('default');
        }
        return $this->connection;
    }

    /**
     * @param string $sql
     * @param array $params
     * @param bool $isQuery
     * @return mixed
     */
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

    /**
     * @param string $table
     * @param string $columnName
     * @param string $function
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
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

    /**
     * @param array $columns
     * @return array
     */
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

    /**
     * @return bool
     */
    private function isConnectionPoolEnabled() {
        if ($this->isConnectionPoolEnabled === null) {
            $this->isConnectionPoolEnabled = Config::getBool(
                'hyperframework.db.enable_connection_pool', true
            );
        }
        return $this->isConnectionPoolEnabled;
    }

    /**
     * @return DbConnectionFactory
     */
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
