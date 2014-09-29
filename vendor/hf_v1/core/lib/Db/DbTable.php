<?php
namespace Hyperframework\Db;

class DbTable {
    public static function getColumnById($id, $selector) {
        return DbClient::getColumnById(static::getName(), $id, $selector);
    }

    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getName(), $id, $selector);
    }

    public static function getColumnByColumns($columns, $selector) {
        return DbClient::getColumnByColumns(static::getName(), $columns, $selector);
    }

    public static function save(&$row) {
        DbClient::save(static::getName(), $row);
    }

    public static function deleteById($id) {
        return DbClient::deleteById(static::getName(), $id);
    }

    public static function getRowByColumns($table, $columns, $selector = '*') {
        return DbClient::getRowByColumns(
            static::getName(), $columns, $selector = '*'
        );
    }

    public static function getAllByColumns($columns, $selector = '*') {
        return DbClient::getAllByColumns(
            static::getName(), $columns, $selector = '*'
        );
    }

    public static function insert($row) {
        return DbClient::insert(static::getName());
    }

    public static function update($table, $columns, $where/*, $mixed, ...*/) {
    }

    public static function updateByColumns(
        $table, $replacementColumns, $filterColumns
    ) {
    }

    public static function delete($table, $where/*, $mixed, ...*/) {
    }

    public static function deleteById($table, $id) {
    }

    public static function deleteByColumns($table, $columns) {
    }

    protected static function getName() {
        $class = get_called_class();
        $position = strrpos($class, '\\');
        if ($position !== false) {
            return substr($class, $position + 1);
        }
        return $class;
    }

    protected static function validate() {
    }

    protected static function onUpdating() {
    }

    protected static function onUpdated() {
    }

    protected static function onInserting() {
    }

    protected static function onInserted() {
    }

    protected static function onDeleting() {
    }

    protected static function onDeleted() {
    }
}
