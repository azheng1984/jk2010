<?php
namespace Hft\Models;

class Article extends DbModel {
    private static $rules = array(
    );

    public static function getValidationRules() {
        return self::$rules;
    }
}
