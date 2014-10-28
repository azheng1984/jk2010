<?php
namespace Hyperframework\Web;

final class RequestPath {
    private static $path;
    private static $segments;

    public static function get() {
        if (self::$path === null) {
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
            self::$path = $tmp[0];
            if (self::$path === '') {
                self::$path = '/';
            } elseif (strpos(self::$path, '//') !== false) {
                self::$path = preg_replace('#/{2,}#', '/', self::$path);
            }
        }
        return self::$path;
    }

    public static function getSegments() {
        if (self::$segments === null) {
            $path = static::get();
            if ($path === '/') {
                self::$segments = array();
            }
            self::$segments = explode('/', trim($path, '/'));
        }
        return self::$segments;
    }
}
