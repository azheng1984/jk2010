<?php
namespace Hyperframework\Web;

class ApplicationInfo {
    private static $cache;

    public function initialize($cache) {
        static::$cache = $cache;
    }

    public static function get($path = null) {
        if ($path === null) {
            $segmentList = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segmentList[0];
        }
        if (static::$cache === null) {
            static::$cache = require
                CACHE_PATH . 'application_info.cache.php';
        }
        if (isset(static::$cache[$path]) === false) {
            throw new NotFoundException(
                'Application path \'' . $path . '\' not found'
            );
        }
        return static::$cache[$path];
    }
}
