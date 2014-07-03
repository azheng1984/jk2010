<?php
namespace Hyperframework\Db;

use Hyperframework\Validator;

class DbModel {
    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getTableName(), $id, $selector);
    }

    public static function save(&$row, $options = null) {
        static::validate($row);
        return DbSaveCommand::save(static::getTableName(), $row, $options);
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

    protected static function validate($row) {
        $rules = static::getValidationRules();
        if ($rules !== null) {
            Validator::run($row, $rules);
        }
    }

    protected static function getValidationRules() {}
}
