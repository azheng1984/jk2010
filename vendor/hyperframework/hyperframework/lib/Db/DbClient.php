<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbClient {
    private static $engine;

    public static function findColumn($sql/*, ...*/) {
        return static::getEngine()->findColumn(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findColumnByColumns(
        $table, array $columns, $selectedColumnName
    ) {
        return static::getEngine()->findColumnByColumns(
            $table, $columns, $selectedColumnName
        );
    }

    public static function findColumnById($table, $id, $selectedColumnName) {
        return static::getEngine()->findColumnById(
            $table, $id, $selectedColumnName
        );
    }

    public static function findRow($sql/*, ...*/) {
        return static::getEngine()->findRow(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findRowByColumns(
        $table, array $columns, array $selectedColumnNames = null
    ) {
        return static::getEngine()->findRowByColumns(
            $table, $columns, $selectedColumnNames
        );
    }

    public static function findRowById(
        $table, $id, $selectedColumnNames = null
    ) {
        return static::getEngine()->findRowById(
            $table, $id, $selectedColumnNames
        );
    }

    public static function findAll($sql/*, ...*/) {
        return static::getEngine()->findAll(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findAllByColumns(
        $table, array $columns, array $selectedColumnNames = null
    ) {
        return static::getEngine()->findAllByColumns(
            $table, $columns, $selectedColumnNames
        );
    }

    public static function find($sql/*, ...*/) {
        return static::getEngine()->find(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function findByColumns(
        $table, array $columns, array $selectedColumnNames = null
    ) {
        return static::getEngine()->findByColumns(
            $table, $columns, $selectedColumnNames
        );
    }

    public static function count($table, $where = null/*, ...*/) {
        return static::getEngine()->count(
            $table, $where, self::getParams(func_get_args(), 2)
        );
    }

    public static function min($table, $columnName, $where = null/*, ...*/) {
        return static::getEngine()->min(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function max($table, $columnName, $where = null/*, ...*/) {
        return static::getEngine()->max(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function sum($table, $columnName, $where = null/*, ...*/) {
        return static::getEngine()->sum(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function average(
        $table, $columnName, $where = null/*, ...*/
    ) {
        return static::getEngine()->average(
            $table, $columnName, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function insert($table, array $row) {
        static::getEngine()->insert($table, $row);
    }

    public static function update($table, array $columns, $where/*, ...*/) {
        return static::getEngine()->update(
            $table, $columns, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function delete($table, $where/*, ...*/) {
        return static::getEngine()->delete(
            $table, $where, self::getParams(func_get_args(), 2)
        );
    }

    public static function deleteById($table, $id) {
        return static::getEngine()->deleteById($table, $id);
    }

    public static function save($table, array &$row) {
        return static::getEngine()->save($table, $row);
    }

    public static function execute($sql/*, ...*/) {
        return static::getEngine()->execute(
            $sql, self::getParams(func_get_args())
        );
    }

    public static function getLastInsertId() {
        return static::getEngine()->getLastInsertId();
    }

    public static function beginTransaction() {
        return static::getEngine()->beginTransaction();
    }

    public static function commit() {
        return static::getEngine()->commit();
    }

    public static function rollback() {
        return static::getEngine()->rollback();
    }

    public static function inTransaction() {
        return static::getEngine()->inTransaction();
    }

    public static function quoteIdentifier($identifier) {
        return static::getEngine()->quoteIdentifier($identifier);
    }

    public static function prepare($sql, array $driverOptions = []) {
        return static::getEngine()->prepare($sql, $driverOptions);
    }

    public static function getConnection() {
        return static::getEngine()->getConnection();
    }

    public static function setConnection($value) {
        static::getEngine()->setConnection($value);
    }

    public static function connect($name) {
        static::getEngine()->connect($name);
    }

    public static function getEngine() {
        if (self::$engine === null) {
            $class = Config::getString(
                'hyperframework.db.client.engine_class', ''
            );
            if ($class === '') {
                self::$engine = new DbClientEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Database client engine class"
                            . " '$class' does not exist, set using config "
                            . "'hyperframework.db.client.engine_class'."
                    );
                }
                self::$engine = new $class;
            }
        }
        return self::$engine;
    }

    public static function setEngine($value) {
        self::$engine = $value;
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
