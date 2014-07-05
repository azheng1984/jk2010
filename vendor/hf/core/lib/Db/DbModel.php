<?php
namespace Hyperframework\Db;

use Hyperframework\Validator;

class DbModel {
    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getTableName(), $id, $selector);
    }

    public static function save(&$row) {
        return DbSaveCommand::save(static::getTableName(), $row);
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

    protected static function isValid($row, &$errors = null) {
        $rules = static::getValidationRules();
        if ($rules !== null) {
            $errors = Validator::run($row, $rules);
        }
        return $errors === null;
    }

    protected static function getValidationRules() {}
}
