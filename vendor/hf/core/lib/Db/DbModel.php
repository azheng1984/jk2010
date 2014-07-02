<?php
namespace Hyperframework\Db;

class DbModel {
    public static function getRowById($id, $selector = '*') {
    }

    public static function insert($row) {
        DbClient::insert(static::getTableName());
    }

    public static function update($row, $where/*, $mixed, ...*/) {
        $args = func_get_args();
        array_shift($args, static::getTableName());
        call_user_func_array('Hyperframework\Db\DbClient::update', $args);
    }

    public static function save(&$row, $options = null) {
        DbSaveCommand::save(static::getTableName(), $row, $options);
    }

    public static function delete($where/*, $mixed, ...*/) {
        $args = func_get_args();
        array_shift($args, static::getTableName());
        call_user_func_array('Hyperframework\Db\DbClient::delete', $args);
    }

    public static function deleteById($id) {
    }

    protected static function getTableName() {
        return get_called_class();
    }
}
