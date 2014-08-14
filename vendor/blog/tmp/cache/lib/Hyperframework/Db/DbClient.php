<?php
namespace Hyperframework\Db;

class DbClient {
    public static function getColumn($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchColumn();
    }

    public static function getColumnByColumns($table, $columns, $selector) {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetchColumn();
    }

    public static function getColumnById($table, $id, $selector) {
        $sql = 'SELECT ' . $selector . ' FROM '
            . static::getConnection()->quoteIdentifier($table)
            . ' WHERE id = ?';
        return static::getColumn($sql, $id);
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRowByColumns($table, $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRowById($table, $id, $selector = '*') {
        $sql = 'SELECT ' . $selector . ' FROM '
            . static::getConnection()->quoteIdentifier($table)
            . ' WHERE id = ?';
        return static::getRow($sql, $id);
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllByColumns($table, $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastInsertId() {
        return static::getConnection()->lastInsertId();
    }

    public static function beginTransaction() {
        return static::getConnection()->beginTransaction();
    }

    public static function commit() {
        return static::getConnection()->commit();
    }

    public static function rollback() {
        return static::getConnection()->rollBack();
    }

    public static function prepare($sql, $isEmulated = false) {
        $driverOptions = array(
            PDO::ATTR_EMULATE_PREPARES => $isEmulated,
        );
        return static::getConnection()->prepare($sql, $driverOptions);
    }

    public static function execute($sql/*, $mixed, ...*/) {
        $params = func_get_args();
        $sql = array_shift($params);
        static::send($sql, $params);
    }

    public static function insert($table, $row) {
        $keys = array();
        foreach (array_keys($row) as $key) {
            $keys[] = static::getConnection()->quoteIdentifier($key);
        }
        $sql = 'INSERT INTO ' . static::getConnection()->quoteIdentifier($table)
            . '(' . implode($keys, ', ') . ') VALUES('
            . static::getParamPlaceholders(count($row)) . ')';
        static::send($sql, array_values($row));
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        $params = array_values($columns);
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_merge(
                $row, array_slice(func_get_args(), 3)
            );
        }
        $tmp = null;
        $connetction = static::getConnection();
        foreach (array_keys($columns) as $key) {
            $tmp .= $connection->quoteIdentifier($key) . ' = ?';
        }
        $sql = 'UPDATE ' . $connection->quoteIdentifier($table)
            . ' SET ' . $tmp . $where;
        static::send($sql, $params));
    }

    public static function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
        list($where, $params) = self::buildWhereByColumns($filterColumns);
        call_user_func_array(
            'static::update',
            array($table, $replacementColumns, $where) + $params
        );
    }

    public static function updateById($table, $columns, $id) {
        static::update($table, $columns, 'id = ?', $id);
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        $params = array();
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_slice(func_get_args(), 2);
        }
        $sql = 'DELETE FROM ' . static::getConnection()->quoteIdentifier($table)
            . $where;
        static::send($sql, $params);
    }

    public static function deleteByColumns($table, $columns) {
        list($where, $params) = self::buildWhereByColumns($columns);
        static::send(
            'DELETE FROM ' . static::getConnection()->quoteIdentifier($table)
                . ' WHERE ' . $where, $params
        );
    }

    public static function deleteById($table, $id) {
        static::delete($table, 'id = ?', $id);
    }

    public static function save($table, &$row, $options = null) {
        return DbSaveCommand::run($table, $row, $options);
    }

    protected static function getConnection() {
        return Connection::getCurrent();
    }

    protected static function send($sql, $params) {
        $connection = static::getConnection();
        if ($params === null || count($parameters) === 0) {
            return $isQuery ?
                $connection->query($sql) : $connection->exec($sql);
        }
        if (is_array($params[0])) {
            $params = $parameters[0];
        }
        $statement = $connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    private static function query($params) {
        $sql = array_shift($params);
        return static::send($sql, $params);
    }

    private static function queryByColumns($table, $columns, $selector) {
        list($where, $params) = self::buildWhereByColumns($columns);
        $sql = 'SELECT ' . $selector . ' FROM '
            . static::getConnection()->quoteIdentifier($table);
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        array_unshift($params, $sql);
        return self::query($params);
    }

    private static function buildWhereByColumns($columns) {
        $params = array();
        $where = null;
        $connection = static::getConnection();
        foreach ($columns as $key => $value) {
            $params[] = $value; 
            if ($where !== null) {
                $where = ' AND ';
            }
            $where .= $connection->quoteIdentifier($key) . ' = ?';
        }
        if ($where === null) {
            throw new \Exception;
        }
        return array($where, $params);
    }

    private static function getParamPlaceholders($count) {
        if ($count > 1) {
            return str_repeat('?, ', $count - 1) . '?';
        }
        if ($count === 1) {
            return '?';
        }
        return '';
    }
}
