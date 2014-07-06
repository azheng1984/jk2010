<?php
namespace Hft\Models;

use Hyperframework\Validator;

class Article extends DbModel {
    private static $rules;

    public static function isValid($row, &$errors) {
        return Validator::run(static::getValidationRules(), $row, $errors);
    }

    public static function getValidationRules() {
        if (self::$rules === null) {
            self::$rules = [];
        }
        return self::$rules;
    }
}
