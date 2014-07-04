<?php
namespace Hyperframework\Db;

use Hyperframework\Validator;
use Hyperframework\ValidationException;

class DbModel {
    public static function getRowById($id, $selector = '*') {
        return DbClient::getRowById(static::getTableName(), $id, $selector);
    }

    public static function save(&$row) {
        $errors = static::validate($row);
        if ($errors !== null) {
            throw new ValidationException($errors);
        }
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

    protected static function validate($row) {
        $rules = static::getValidationRules();
        if ($rules !== null) {
            return Validator::run($row, $rules);
        }
    }

    protected static function getValidationRules() {}
}
