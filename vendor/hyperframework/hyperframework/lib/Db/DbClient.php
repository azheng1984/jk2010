<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class DbClient {
    /**
     * @param string $sql
     * @param array $params
     * @return mixed
     */
    public static function findColumn($sql, array $params = null) {
        return static::getEngine()->findColumn($sql, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param array $columns
     * @return mixed
     */
    public static function findColumnByColumns(
        $table, $columnName, array $columns
    ) {
        return static::getEngine()->findColumnByColumns(
            $table, $columnName, $columns
        );
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param mixed $id
     * @return mixed
     */
    public static function findColumnById($table, $columnName, $id) {
        return static::getEngine()->findColumnById($table, $columnName, $id);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function findRow($sql, array $params) {
        return static::getEngine()->findRow($sql, $params);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return array
     */
    public static function findRowByColumns(
        $table, array $columns, array $select = null
    ) {
        return static::getEngine()->findRowByColumns($table, $columns, $select);
    }

    /**
     * @param string $table
     * @param mixed $id
     * @param array $select
     * @return array
     */
    public static function findRowById($table, $id, array $select = null) {
        return static::getEngine()->findRowById($table, $id, $select);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array[]
     */
    public static function findAll($sql, array $params = null) {
        return static::getEngine()->findAll($sql, $params);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return array[]
     */
    public static function findAllByColumns(
        $table, array $columns, array $select = null
    ) {
        return static::getEngine()->findAllByColumns($table, $columns, $select);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return DbStatement
     */
    public static function find($sql, array $params) {
        return static::getEngine()->find($sql, $params);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param array $select
     * @return DbStatement
     */
    public static function findByColumns(
        $table, array $columns, array $select = null
    ) {
        return static::getEngine()->findByColumns($table, $columns, $select);
    }

    /**
     * @param string $table
     * @param string|array $where
     * @param array $params
     * @return int
     */
    public static function count($table, $where = null, array $params = null) {
        return static::getEngine()->count(
            $table, $where, $params
        );
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public static function min(
        $table, $columnName, $where = null, array $params = null
    ) {
        return static::getEngine()->min($table, $columnName, $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public static function max(
        $table, $columnName, $where = null, array $params = null
    ) {
        return static::getEngine()->max($table, $columnName, $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public static function sum(
        $table, $columnName, $where = null, array $params = null
    ) {
        return static::getEngine()->sum($table, $columnName, $where, $params);
    }

    /**
     * @param string $table
     * @param string $columnName
     * @param string|array $where
     * @param array $params
     * @return mixed
     */
    public static function average(
        $table, $columnName, $where = null, array $params = null
    ) {
        return static::getEngine()->average(
            $table, $columnName, $where, $params
        );
    }

    /**
     * @param string $table
     * @param array $row
     */
    public static function insert($table, array $row) {
        static::getEngine()->insert($table, $row);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param string|array $where
     * @param array $params
     */
    public static function update(
        $table, array $columns, $where, array $params = null
    ) {
        return static::getEngine()->update($table, $columns, $where, $params);
    }

    /**
     * @param string $table
     * @param array $columns
     * @param mixed $id
     */
    public static function updateById($table, array $columns, $id) {
        return static::getEngine()->updateById($table, $columns, $id);
    }

    /**
     * @param string $table
     * @param string|array $where
     * @param array $params
     */
    public static function delete($table, $where, array $params = null) {
        return static::getEngine()->delete($table, $where, $params);
    }

    /**
     * @param string $table
     * @param mixed $id
     */
    public static function deleteById($table, $id) {
        return static::getEngine()->deleteById($table, $id);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return int
     */
    public static function execute($sql, array $params = null) {
        return static::getEngine()->execute(
            $sql, $params
        );
    }

    /**
     * @return mixed
     */
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

    /**
     * @return bool
     */
    public static function inTransaction() {
        return static::getEngine()->inTransaction();
    }

    /**
     * @param string $identifier
     * @return string
     */
    public static function quoteIdentifier($identifier) {
        return static::getEngine()->quoteIdentifier($identifier);
    }

    /**
     * @param string $sql
     * @param array $driverOptions
     * @return DbStatement
     */
    public static function prepare($sql, array $driverOptions = []) {
        return static::getEngine()->prepare($sql, $driverOptions);
    }

    /**
     * @param bool $shouldConnect
     * @return DbConnection
     */
    public static function getConnection($shouldConnect = true) {
        return static::getEngine()->getConnection($shouldConnect);
    }

    /**
     * @param DbConnection $connection
     */
    public static function setConnection(DbConnection $connection) {
        static::getEngine()->setConnection($connection);
    }

    /**
     * @param string $name
     */
    public static function connect($name) {
        static::getEngine()->connect($name);
    }

    /**
     * @param string $name
     */
    public static function closeConnection($name = null) {
        static::getEngine()->closeConnection($name);
    }

    /**
     * @return object
     */
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

    /**
     * @param object $engine
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.db.client_engine', $engine);
    }
}
