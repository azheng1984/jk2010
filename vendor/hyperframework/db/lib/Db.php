<?php
class Db {
    public static function getColumn($sql/*, $parameter, ...*/) {
        return self::call(func_get_args())->fetchColumn();
    }

    public static function getRow($sql/*, $parameter, ...*/) {
        return self::call(func_get_args())->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll($sql/*, $parameter, ...*/) {
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

    public static function execute($sql/*, $parameter, ...*/) {
        return self::call(func_get_args());
    }

    public static function insert($table, $columnList) {
        self::execute(
            'INSERT INTO '.$table.'('.implode(array_keys($columnList), ', ')
            .') VALUES('.self::getParameterMarkerList(count($columnList)).')',
                array_values($columnList)
            );
    }

    public static function update(
        $table, $columnList, $where/*, $parameter, ...*/
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

    public static function delete($table, $where/*, $parameter, ...*/) {
        $parameterList = array();
        if ($where !== null) {
            $where = ' WHERE '.$where;
            $parameterList = array_slice(func_get_args(), 2);
        }
        self::execute('DELETE FROM '.$table.$where, $parameterList);
    }

    private static function call($parameterList) {
        $connection = DbConnection::getCurrent();
        $sql = array_shift($parameterList);
        //echo $sql.PHP_EOL;
        if (isset($parameterList[0]) && is_array($parameterList[0])) {
            $parameterList = $parameterList[0];
        }
        $statement = $connection->prepare($sql);
        $isExit = false;
        if (is_array($parameterList) === false) {
            echo date('Y-m-d H:i:s');
            $isExit = true;
        }
        $statement->execute($parameterList);
        if ($isExit) {
            exit;
        }
        return $statement;
    }

    private static function getParameterMarkerList($columnAmount) {
        if ($columnAmount > 1) {
            return str_repeat('?, ', $columnAmount - 1).'?';
        }
        if ($columnAmount === 1) {
            return '?';
        }
        return '';
    }
}
