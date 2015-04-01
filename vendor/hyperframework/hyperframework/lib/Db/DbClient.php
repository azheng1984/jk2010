<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbClient {
    public static function findColumn($sql/*, ...*/) {
        return static::getEngine()->findColumn(
            $sql, self::getParams(func_get_args(), 1)
        );
    }

    public static function findColumnByColumns($table, $columnName, $columns) {
        return static::getEngine()->findColumnByColumns(
            $table, $columnName, $columns
        );
    }

    public static function findColumnById($table, $columnName, $id) {
        return static::getEngine()->findColumnById($table, $columnName, $id);
    }

    public static function findRow($sql/*, ...*/) {
        return static::getEngine()->findRow(
            $sql, self::getParams(func_get_args(), 1)
        );
    }

    public static function findRowByColumns($table, $columns, $select = null) {
        return static::getEngine()->findRowByColumns($table, $columns, $select);
    }

    public static function findRowById($table, $id, $select = null) {
        return static::getEngine()->findRowById($table, $id, $select);
    }

    public static function findAll($sql/*, ...*/) {
        return static::getEngine()->findAll(
            $sql, self::getParams(func_get_args(), 1)
        );
    }

    public static function findAllByColumns($table, $columns, $select = null) {
        return static::getEngine()->findAllByColumns($table, $columns, $select);
    }

    public static function find($sql/*, ...*/) {
        return static::getEngine()->find(
            $sql, self::getParams(func_get_args(), 1)
        );
    }

    public static function findByColumns($table, $columns, $select = null) {
        return static::getEngine()->findByColumns($table, $columns, $select);
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

    public static function insert($table, $row) {
        static::getEngine()->insert($table, $row);
    }

    public static function update($table, $columns, $where/*, ...*/) {
        return static::getEngine()->update(
            $table, $columns, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function updateById($table, $columns, $id) {
        return static::getEngine()->updateById($table, $columns, $id);
    }

    public static function delete($table, $where/*, ...*/) {
        return static::getEngine()->delete(
            $table, $where, self::getParams(func_get_args(), 2)
        );
    }

    public static function deleteById($table, $id) {
        return static::getEngine()->deleteById($table, $id);
    }

    public static function execute($sql/*, ...*/) {
        return static::getEngine()->execute(
            $sql, self::getParams(func_get_args(), 1)
        );
    }

    public static function getLastInsertId() {
        return static::getEngine()->getLastInsertId();
    }

    public static function beginTransaction() {
        static::getEngine()->beginTransaction();
    }

    public static function commit() {
        static::getEngine()->commit();
    }

    public static function rollback() {
        static::getEngine()->rollback();
    }

    public static function inTransaction() {
        return static::getEngine()->inTransaction();
    }

    public static function quoteIdentifier($identifier) {
        return static::getEngine()->quoteIdentifier($identifier);
    }

    public static function prepare($sql, $driverOptions = []) {
        return static::getEngine()->prepare($sql, $driverOptions);
    }

    public static function getConnection($shouldConnect = true) {
        return static::getEngine()->getConnection($shouldConnect);
    }

    public static function setConnection($connection) {
        static::getEngine()->setConnection($connection);
    }

    public static function connect($name) {
        static::getEngine()->connect($name);
    }

    public static function closeConnection($name = null) {
        static::getEngine()->closeConnection($name);
    }

    public static function getEngine() {
        $engine = Registry::get('hyperframework.db.client_engine');
        if ($engine === null) {
            $configName = 'hyperframework.db.client_engine_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $engine = new DbClientEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist, "
                            . "set using config '$configName'."
                    );
                }
                $engine = new $class;
            }
            static::setEngine($engine);
        }
        return $engine;
    }

    public static function setEngine($engine) {
        Registry::set('hyperframework.db.client_engine', $engine);
    }

    private static function getParams($args, $offset) {
        if (isset($args[$offset]) === false) {
            return;
        }
        if (is_array($args[$offset])) {
            return $args[$offset];
        }
        return array_slice($args, $offset);
    }
}
