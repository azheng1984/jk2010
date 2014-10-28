<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetPathPrefix {
    private static $value;

    public static function get() {
        if (self::$value !== null) {
            return self::$value;
        }
        self::$value = Config::get('hyperframework.asset.path_prefix');
        if (self::$value === null) {
            self::$value = '/cache';
        }
        return self::$value;
    }
}
