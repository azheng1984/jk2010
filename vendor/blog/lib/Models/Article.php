<?php
namespace Hyperframework\Blog\Models;

use Hyperframework\Validator;

final class Article extends \Hyperframework\Db\DbModel {
    private static $validationRules;

    public static function isValid($row, &$errors) {
        return Validator::run(self::getValidationRules(), $row, $errors);
    }

    public static function getValidationRules() {
        if (self::$validationRules === null) {
            self::$validationRules = [];
        }
        return self::$validatonRules;
    }
}
