<?php
namespace Hyperframework\Web;

class RequestPath {
    private static $path;
    private static $lastSlashRemovedPath;
    private static $segments;

    public static function get($shouldKeepLastSlash = true) {
        if (self::$path === null) {
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
            self::$path = $tmp[0];
            if (self::$path === '') {
                self::$path = '/';
            } elseif (strpos(self::$path, '//') !== false) {
                self::$path = preg_replace('#/{2,}#', '/', self::$path);
            }
        }
        if ($shouldKeepLastSlash) {
            return self::$path;
        }
        if (self::$lastSlashRemovedPath === null) {
            self::$lastSlashRemovedPath = '/' . trim(self::$path, '/');
        }
        return self::$lastSlashRemovedPath;
    }

    public static function getSegments() {
        if (self::$segments === null) {
            $path = static::get(false);
            if ($path === '/') {
                self::$segments = [];
            }
            self::$segments = explode('/', $path);
        }
        return self::$segments;
    }
}
