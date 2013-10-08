<?php
class Asset {
    private static $cache;

    private static function getCache() {
        if (self::$cache === null) {
            self::$cache = require CACHE_PATH.'asset.cache.php';
        }
        return self::$cache;
    }

    private static function getConfig() {
        if (self::$cache === null) {
            self::$cache = require CACHE_PATH.'asset.cache.php';
        }
        return self::$cache;
    }

    private static function getVersion($path) {
        $cache = self::getCache();
        if (isset($cache[$path])) {
            return $cache[$path];
        }
    }

    public static function getUrl($path) {
        //根据 include path 
    }
}
