<?php
namespace Hyperframework\Db;

use Hyperframework\Validation;

trait DbValidationTrait {
    private static $validationRules;

    public static function isValid($row, &$errors = null) {
        $rules = static::getValidationRules();
        if ($rules !== null) {
            $errors = Validator::run($row, $rules);
        }
        return $errors === null;
    }

    public static function getValidationRules() {
        if (self::$validationRules === null) {
            self::$validationRules = static::buildValidationRules();
            if (self::$validationRules === null) {
                self::$validationRules = array();
            }
        }
        return self::$validationRules;
    }

    protected static function buildValidationRules() {}
}
