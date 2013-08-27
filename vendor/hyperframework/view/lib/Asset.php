<?php
class Asset {
    private static $cache;

    private static function getCache() {
        if (self::$cache === null) {
            self::$cache = require CACHE_PATH.'asset.cache.php';
        }
        return self::$cache;
    }

    public static function getMd5($path) {
        $cache = self::getCache();
        if (isset($cache[$path])) {
            return $cache[$path];
        }
    }

    public static function getDigest($path) {
    }

    public static function renderJsLink() {
    }

    public static function renderCssLink() {
    }
}
