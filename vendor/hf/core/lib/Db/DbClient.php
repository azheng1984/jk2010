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
        return static::getColumn(
            'SELECT ' . $selector. ' FROM ' . $table . ' WHERE id = ?', $id
        );
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRowByColumns($table, $columns, $selector = '*') {
        $result = self::queryByColumns($table, $columns, $selector);
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRowById($table, $id, $selector = '*') {
        return static::getRow(
            'SELECT ' . $selector . ' FROM ' . $table . ' WHERE id = ?' , $id
        );
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
        $sql = 'INSERT INTO ' . $table . '('
            . implode(array_keys($row), ', ') . ') VALUES('
            . static::getParamPlaceholders(count($row)) . ')';
        static::send($sql, array_values($row));
    }

    public static function update($table, $row, $where/*, $mixed, ...*/) {
        $params = array_values($row);
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_merge(
                $row, array_slice(func_get_args(), 3)
            );
        }
        $sql = 'UPDATE ' . $table . ' SET '
            . implode(array_keys($row), ' = ?, ') . ' = ?' . $where;
        static::send($sql, $params));
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        $params = array();
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $params = array_slice(func_get_args(), 2);
        }
        $sql = 'DELETE FROM ' . $table . $where;
        static::send($sql, $params);
    }

    public static function deleteByColumns($table, $columns) {
        list($where, $params) = self::buildWhereByColumns($columns);
        static::send(
            'DELETE FROM ' . $table . ' WHERE ' . $where, $params
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
        $sql = 'SELECT ' . $selector . ' FROM ' . $table;
        if ($where !== null) {
            $sql .= ' WHERE ' . $where;
        }
        array_unshift($params, $sql);
        return self::query($params);
    }

    private static function buildWhereByColumns($columns) {
        $params = array();
        $where = null;
        foreach ($columns as $key => $value) {
            $params[] = $value; 
            if ($where === null) {
                $where = $key . ' = ?';
                continue;
            }
            $where .= ' AND ' . $key . ' = ?';
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
