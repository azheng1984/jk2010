<?php
namespace Hyperframework\Web;

class ApplicationCacheProvider {
    private static $cache;
    private static $path;

    public static function initialize($path = null) {
        static::$cache = null;
        static::$path = $path;
    }

    public function get() {
        if (static::$cache === null) {
            $path = static::$path === null ?
                CACHE_PATH . 'application.cache.php' : static::$path;
            static::$cache = require $path;
        }
        return static::$cache;
    }
}
