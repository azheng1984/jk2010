<?php
namespace Hyperframework\Db;

class DbClient {
    public static function getColumn($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchColumn();
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public static function getRowById($table, $id, $selector = '*') {
        return static::query(
            'SELECT ' . $selector . ' FROM ' . $table . ' WHERE id = ?' , $id
        );
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return static::query(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
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
        $parameters = func_get_args();
        $sql = array_shift($parameters);
        return static::send($sql, $parameters, false);
    }

    public static function insert($table, $columns) {
        $sql = 'INSERT INTO ' . $table . '(' .
            implode(array_keys($parameters), ', ') . ') VALUES(' .
            static::getParameterPlaceholders(count($parameters)) . ')';
        static::send($sql, array_values($columns), false, true);
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        $parameters = array_values($columns);
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $parameters = array_merge(
                $parameters, array_slice(func_get_args(), 3)
            );
        }
        $sql = 'UPDATE ' . $table . ' SET '
            . implode(array_keys($columns), ' = ?, ') . ' = ?' . $where;
        static::send($sql, $parameters), false);
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        $parameters = array();
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $parameters = array_slice(func_get_args(), 2);
        }
        $sql = 'DELETE FROM ' . $table . $where;
        static::send($sql, $parameters, false);
    }

    public static function deleteById($table, $id) {
        static::delete($table, 'id = ?', $id);
    }

    public static function save($table, $row, $options = null) {
        DbSaveCommand::run($table, $row, $options);
    }

    protected static function getConnection() {
        return Connection::getCurrent();
    }

    protected static function send(
        $sql, $parameters, $isQuery = true, $isInsert = false
    ) {
        $connection = static::getConnection();
        if ($parameters === null || count($parameters) === 0) {
            return $isQuery ?
                $connection->query($sql) : $connection->exec($sql);
        }
        if (is_array($parameters[0])) {
            $parameters = $parameters[0];
        }
        $statement = $connection->prepare($sql);
        $statement->execute($parameters);
        if ($isQuery) {
            return $statement;
        }
        if ($isInsert === false) {
            return $statement->rowCount();
        }
    }

    private static function query($arguments) {
        $parameters = $arguments();
        $sql = array_shift($parameters);
        return static::send($sql, $parameters);
    }

    private static function getParameterPlaceholders($amount) {
        if ($amount > 1) {
            return str_repeat('?, ', $amount - 1) . '?';
        }
        if ($amount === 1) {
            return '?';
        }
        return '';
    }
}
