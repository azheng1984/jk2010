<?php
namespace Hyperframework\Db;

class DbModel {
    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getTableName(), $id, $selector);
    }

    public static function save(&$row, $options = null) {
        return DbSaveCommand::save(static::getTableName(), $row, $options);
    }

    public static function delete($where/*, $mixed, ...*/) {
        $args = func_get_args();
        array_unshift($args, static::getTableName());
        return call_user_func_array(
            'Hyperframework\Db\DbClient::delete', $args
        );
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
