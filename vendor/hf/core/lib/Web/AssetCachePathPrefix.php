<?php
namespace Hyperframework\Web;

class AssetCachePathPrefix {
    private static $value;

    public static function get() {
        if (self::$value === null) {
            return self::$value;
        }
        self::$value = Config::get(
            'hyperframework.web.asset_cache_path_prefix'
        );
        if (self::$value === null) {
            self::$value = '/cache';
        }
        return self::$value;
    }
}
