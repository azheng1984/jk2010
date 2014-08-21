<?php
namespace Hyperframework\Db;

class DbModel {
    public static function getColumnById($id, $selector) {
        return DbClient::getColumnById(static::getTableName(), $id, $selector);
    }

    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getTableName(), $id, $selector);
    }

    public static function save(&$row) {
        return DbClient::save(static::getTableName(), $row);
    }

    public static function deleteById($id) {
        return DbClient::deleteById(static::getTableName(), $id);
    }

    protected static function getTableName() {
        $class = get_called_class();
        $position = strrpos($class, '\\');
        if ($position !== false) {
            return substr($class, $position + 1);
        }
        return $class;
    }
}
