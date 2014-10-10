<?php
namespace Hyperframework\Db;

use Hyperframework\Config;

class DbClient {
    private static $engine;

    public static function getById($table, $id, $columnNameOrNames = null) {
        return $this->getEngine()->getById(
            $table, $id, $columnNameOrNames = null
        );
    }

    public static function getColumn($sql/*, $mixed, ...*/) {
        return $this->getEngine()->getColumn($sql, $params);
    }

    public static function getColumnByColumns($table, $columns, $columnName) {
        return $this->getEngine()->getColumnByColumns(
            $table, $columns, $columnName
        );
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return $this->getEngine()->getRow($sql, $params);
    }

    public static function getRowByColumns(
        $table, $columns, $columnNames = null
    ) {
        return $this->getEngine()->getRowByColumns(
            $table, $columns, $columnNames
        );
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return $this->getEngine()->getAll($sql, $params);
    }

    public static function getAllByColumns(
        $table, $columns, $columnNameOrNames = null
    ) {
        return $this->getEngine()->getAllByColumns(
            $table, $columns, $columnNameOrNames
        );
    }

    public static function count($table) {
        return $this->getEngine()->count($table);
    }

    public static function min($table, $columnName) {
        return $this->getEngine()->min($table, $columnName);
    }

    public static function max($table, $columnName) {
        return $this->getEngine()->max($table, $columnName);
    }

    public static function sum($table, $columnName) {
        return $this->getEngine()->sum($table, $columnName);
    }

    public static function average($table, $columnName) {
        return $this->getEngine()->average($table, $columnName);
    }

    public static function insert($table, $row) {
        return $this->getEngine()->insert($table, $row);
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        return $this->getEngine()->update(func_get_args());
    }

    public static function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        return $this->getEngine()->delete(func_get_args());
    }

    public static function deleteByColumns($table, $columns) {
    }

    public static function deleteById($table, $id) {
    }

    public static function save($table, array &$row) {
    }

    public static function execute($sql/*, $mixed, ...*/) {
        $params = func_get_args();
        $sql = array_shift($params);
        return static::sendSql($sql, $params);
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

    public static function inTransaction() {
        return static::getConnection()->inTransaction();
    }

    public static function quoteIdentifier($identifier) {
        return static::getConnection()->quoteIdentifier($identifier);
    }

    public static function prepare($sql, $driverOptions = array()) {
        return self::getEngine()->prepare($sql, $driverOptions);
    }

    final protected static function sendSql($sql, $params, $isQuery = false) {
        return self::getEngine()->sendSql($sql, $params, $isQuery);
    }

    final protected static function getConnection() {
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
}
