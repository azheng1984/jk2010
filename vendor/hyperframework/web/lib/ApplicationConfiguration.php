<?php
namespace Hyperframework\Web;

class ApplicationConfiguration {
    private static $cache;

    public static function read($path = null) {
        if ($path === null) {
            $segmentList = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segmentList[0];
        }
        if (static::$cache === null) {
            static::$cache = require CACHE_PATH . 'application.cache.php';
        }
        if (isset(static::$cache[$path]) === false) {
            throw new NotFoundException(
                'Application path \'' . $path . '\' not found'
            );
        }
        return static::$cache[$path];
    }
}
