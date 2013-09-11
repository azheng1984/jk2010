<?php
class Db {
    public static function getColumn($sql/*, $mixed, ...*/) {
        return self::call(func_get_args())->fetchColumn();
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return self::call(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return self::call(func_get_args())->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getLastInsertId() {
        return DbConnection::getCurrent()->lastInsertId();
    }

    public static function beginTransaction() {
        return DbConnection::getCurrent()->beginTransaction();
    }

    public static function commit() {
        return DbConnection::getCurrent()->commit();
    }

    public static function rollback() {
        return DbConnection::getCurrent()->rollBack();
    }

    public static function prepare($sql, $driverOptions = array()) {
        return DbConnection::getCurrent()->prepare($sql, $driverOptions);
    }

    public static function execute($sql/*, $mixed, ...*/) {
        return self::call(func_get_args(), false);
    }

    public static function insert($table, $columns) {
        $sql = 'INSERT INTO '.$table.'('.implode(array_keys($parameters), ', ')
            .') VALUES('.self::getParameterPlaceholders(count($parameters)).')';
        $arguments = array_unshift(array_values($columns), $sql);
        self::call($arguments, false, true);
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        $parameterList = array_values($columnList);
        if ($where !== null) {
            $where = ' WHERE '.$where;
            $parameterList = array_merge(
                $parameterList, array_slice(func_get_args(), 3)
            );
        }
        return self::execute(
            'UPDATE ' . $table . ' SET ' . implode(array_keys($columnList), ' = ?, ')
            .' = ?'.$where, $parameterList
        );
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        $parameterList = array();
        if ($where !== null) {
            $where = ' WHERE ' . $where;
            $parameterList = array_slice(func_get_args(), 2);
        }
        return self::execute('DELETE FROM ' . $table . $where, $parameterList);
    }

    private static function call(
        $arguments, $isQuery = true, $isInsert = false
    ) {
        $sql = array_shift($arguments);
        $connection = DbConnection::getCurrent();
        if (count($arguments) === 0) {
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

    private static function getParameterPlaceholders($amount) {
        if ($amount > 1) {
            return str_repeat('?, ', $amount - 1).'?';
        }
        if ($amount === 1) {
            return '?';
        }
        return '';
    }
}
