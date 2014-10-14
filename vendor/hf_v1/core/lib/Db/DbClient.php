<?php
namespace Hyperframework\Db;

use Hyperframework\Config;

class DbClient {
    private static $engine;

    public static function findById($table, $id, $columnNameOrNames = null) {
        return self::getEngine()->findById($table, $id, $columnNameOrNames);
    }

    public static function findColumn($sql/*, ...*/) {
        return self::getEngine()->findColumn(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findColumnByColumns(
        $table, array $columns, $columnName
    ) {
        return self::getEngine()->findColumnByColumns(
            $table, $columns, $columnName
        );
    }

    public static function findRow($sql/*, ...*/) {
        return self::getEngine()->findRow(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findRowByColumns(
        $table, array $columns, array $columnNames = null
    ) {
        return self::getEngine()->findRowByColumns(
            $table, $columns, $columnNames
        );
    }

    public static function findAll($sql/*, ...*/) {
        return self::getEngine()->findAll(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findAllByColumns(
        $table, array $columns, $columnNameOrNames = null
    ) {
        return self::getEngine()->findAllByColumns(
            $table, $columns, $columnNameOrNames
        );
    }

    public static function count($table, $where = null/*, ...*/) {
        return self::getEngine()->count(
            $table, $where, self::getParams(func_get_args(), 2)
        );
    }

    public static function min($table, $columnName, $where = null/*, ...*/) {
        return self::getEngine()->min(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function max($table, $columnName, $where = null/*, ...*/) {
        return self::getEngine()->max(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function sum($table, $columnName, $where = null/*, ...*/) {
        return self::getEngine()->sum(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function average(
        $table, $columnName, $where = null/*, ...*/
    ) {
        return self::getEngine()->average(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function insert($table, array $row) {
        return self::getEngine()->insert($table, $row);
    }

    public static function update($table, array $columns, $where/*, ...*/) {
        return self::getEngine()->update(
            $table, $columns, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function delete($table, $where/*, ...*/) {
        return self::getEngine()->delete(
            $table, $where, self::getParams(func_get_args(), 2)
        );
    }

    public static function deleteById($table, $id) {
        return self::getEngine()->deleteById($table, $id);
    }

    public static function save($table, array &$row) {
        return self::getEngine()->save($table, $row);
    }

    public static function execute($sql/*, ...*/) {
        return self::getEngine()->execute(
            $sql, self::getParams(func_get_args())
        );
    }
 
    public static function getLastInsertId() {
        return self::getEngine()->lastInsertId();
    }

    public static function beginTransaction() {
        return self::getEngine()->beginTransaction();
    }

    public static function commit() {
        return self::getEngine()->commit();
    }

    public static function rollback() {
        return self::getEngine()->rollback();
    }

    public static function inTransaction() {
        return self::getEngine()->inTransaction();
    }

    public static function quoteIdentifier($identifier) {
        return self::getEngine()->quoteIdentifier($identifier);
    }

    public static function prepare($sql, array $driverOptions = array()) {
        return self::getEngine()->prepare($sql, $driverOptions);
    }

    public static function getConnection() {
        return self::getEngine()->getConnection();
    }

    final protected static function getEngine() {
        if (self::$engine === null) {
            $class = Config::get('hyperframework.db.client.engine');
            if ($class === null) {
                self::$engine = new DbClientEngine;
            } else {
                self::$engine = new $class;
            }
        }
        return self::$engine;
    }

    private static function getParams(array $args, $offset = 1) {
        if (isset($args[$offset]) === false) {
            return;
        }
        if (is_array($args[$offset])) {
            return $args[$offset];
        }
        return array_slice($args, $offset);
    }
}
