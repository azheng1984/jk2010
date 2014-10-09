<?php
namespace Hyperframework\Db;

use Hyperframework\Config;

class DbClient {
    private $engine;

    public static function getById($table, $id, $columnNameOrNames = null) {
        return $this->getHelper()->getById(
            $table, $id, $columnNameOrNames = null
        );
    }

    public static function getColumn($sql/*, $mixed, ...*/) {
        return $this->getHelper()->getColumn($sql, $params);
    }

    public static function getColumnByColumns($table, $columns, $columnName) {
        return $this->getHelper()->getColumnByColumns(
            $table, $columns, $columnName
        );
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return $this->getHelper()->getRow($sql, $params);
    }

    public static function getRowByColumns(
        $table, $columns, $columnNames = null
    ) {
        return $this->getHelper()->getRowByColumns(
            $table, $columns, $columnNames
        );
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return $this->getHelper()->getAll(func_get_args());
    }

    public static function getAllByColumns(
        $table, $columns, $columnNameOrNames = null
    ) {
        return $this->getHelper()->getAllByColumns(
            $table, $columns, $columnNameOrNames
        );
    }

    public static function count($table) {
        return $this->getHelper()->count($table);
    }

    public static function min($table, $columnName) {
        return $this->getHelper()->min($table, $columnName);
    }

    public static function max($table, $columnName) {
        return $this->getHelper()->max($table, $columnName);
    }

    public static function sum($table, $columnName) {
        return $this->getHelper()->sum($table, $columnName);
    }

    public static function average($table, $columnName) {
        return $this->getHelper()->average($table, $columnName);
    }

    public static function insert($table, $row) {
        return $this->getHelper()->insert($table, $row);
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        return $this->getHelper()->update(func_get_args());
    }

    public static function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        return $this->getHelper()->delete(func_get_args());
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
        return self::getHelper()->prepare($sql, $driverOptions);
    }

    final protected static function sendSql($sql, $params, $isQuery = false) {
        return self::getHelper()->sendSql($sql, $params, $isQuery);
    }

    final protected static function getConnection() {
        return self::getHelper()->getConnection();
    }

    final protected static function getHelper() {
        if (self::$helper === null) {
            $class = Config::get('hyperframework.db.client.helper');
            if ($class === null) {
                self::$helper = new DbClientHelper;
            } else {
                self::$helper = new $class;
            }
        }
        return self::$helper;
    }
}
