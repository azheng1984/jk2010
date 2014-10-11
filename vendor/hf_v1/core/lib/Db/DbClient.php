<?php
namespace Hyperframework\Db;

use Hyperframework\Config;

class DbClient {
    private static $engine;

    public static function getById($table, $id, $columnNameOrNames = null) {
        return self::getEngine()->getById(
            $table, $id, $columnNameOrNames
        );
    }

    public static function getColumn($sql/*, $mixed, ...*/) {
        return self::getEngine()->getColumn(
            $sql, $this->getParams(func_get_args())
        );
    }

    public static function getColumnByColumns($table, $columns, $columnName) {
        return self::getEngine()->getColumnByColumns(
            $table, $columns, $columnName
        );
    }

    public static function getRow($sql/*, $mixed, ...*/) {
        return self::getEngine()->getRow(
            $sql, $this->getParams(func_get_args())
        );
    }

    public static function getRowByColumns(
        $table, $columns, $columnNames = null
    ) {
        return self::getEngine()->getRowByColumns(
            $table, $columns, $columnNames
        );
    }

    public static function getAll($sql/*, $mixed, ...*/) {
        return self::getEngine()->getAll(
            $sql, $this->getParams(func_get_args())
        );
    }

    public static function getAllByColumns(
        $table, $columns, $columnNameOrNames = null
    ) {
        return self::getEngine()->getAllByColumns(
            $table, $columns, $columnNameOrNames
        );
    }

    public static function count($table) {
        return self::getEngine()->count($table);
    }

    public static function min($table, $columnName) {
        return self::getEngine()->min($table, $columnName);
    }

    public static function max($table, $columnName) {
        return self::getEngine()->max($table, $columnName);
    }

    public static function sum($table, $columnName) {
        return self::getEngine()->sum($table, $columnName);
    }

    public static function average($table, $columnName) {
        return self::getEngine()->average($table, $columnName);
    }

    public static function insert($table, $row) {
        return self::getEngine()->insert($table, $row);
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
        return self::getEngine()->update(
            $table, $columns, $where, self::getParams(func_get_args(), 3)
        );
    }

    public static function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
        return self::getEngine()->updateByColumns(
            $table, $replacementColumns, $filterColumns
        );
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
        return self::getEngine()->delete(self::getParams(func_get_args(), 2));
    }

    public static function deleteByColumns($table, $columns) {
        return self::getEngine()->deleteByColumns($table, $columns);
    }

    public static function deleteById($table, $id) {
        return self::getEngine()->deleteById($table, $id);
    }

    public static function save($table, array &$row) {
        return self::getEngine()->save($table, $row);
    }

    public static function execute($sql/*, $mixed, ...*/) {
        return self::getEngine()->execute($sql, self::getParams(func_get_args()));
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
        return self::getEngine()->rollBack();
    }

    public static function inTransaction() {
        return self::getEngine()->inTransaction();
    }

    public static function quoteIdentifier($identifier) {
        return self::getEngine()->quoteIdentifier($identifier);
    }

    public static function prepare($sql, $driverOptions = array()) {
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

    private static function getParams($args, $offset = 1) {
        if (isset($args[$offset]) && is_array($args[$offset])) {
            return $args[$offset];
        }
        return array_slice($args, $offset);
    }
}
