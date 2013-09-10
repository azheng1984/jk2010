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

    public static function execute($sql/*, $mixed, ...*/) {
        return self::call(func_get_args());
    }

    public static function insert($table, $columnList) {
        self::execute(
            'INSERT INTO '.$table.'('.implode(array_keys($columnList), ', ')
            .') VALUES('.self::getParameterPlaceholders(count($columnList)).')',
                array_values($columnList)
        );
    }

    public static function update(
        $table, $columns, $where/*, $mixed, ...*/
    ) {
        $parameterList = array_values($columnList);
        if ($where !== null) {
            $where = ' WHERE '.$where;
            $parameterList = array_merge(
                $parameterList, array_slice(func_get_args(), 3)
            );
        }
        self::execute(
            'UPDATE '.$table.' SET '.implode(array_keys($columnList), ' = ?, ')
            .' = ?'.$where, $parameterList
        );
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        $parameterList = array();
        if ($where !== null) {
            $where = ' WHERE '.$where;
            $parameterList = array_slice(func_get_args(), 2);
        }
        self::execute('DELETE FROM '.$table.$where, $parameterList);
    }

    private static function call($arguments) {
        $sql = array_shift($arguments);
        if (isset($parameters[0]) && is_array($parameters[0])) {
            $parameters = $parameters[0];
        }
        $connection = DbConnection::getCurrent();
        $statement = $connection->prepare($sql);
        $statement->execute($parameters);
        return $statement;
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
