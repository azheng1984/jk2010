<?php
namespace Hft\Models;

use Hyperframework\Validator;

class Article extends DbModel {
    private static $validationRules;

    public static function isValid($row, &$errors) {
        return Validator::run(static::getValidationRules(), $row, $errors);
    }

    public static function getValidationRules() {
        if (self::$validationRules === null) {
            self::$validationRules = [];
        }
        return self::$validatonRules;
    }
}
