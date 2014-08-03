<?php
namespace Hyperframework\Web;

use Hyperframework\Config;

class AssetCachePathPrefix {
    private static $value;

    public static function get() {
        if (self::$value !== null) {
            return self::$value;
        }
        self::$value = Config::get('hyperframework.asset_cache_path_prefix');
        if (self::$value === null) {
            self::$value = '/cache/';
        }
        return self::$value;
    }
}
