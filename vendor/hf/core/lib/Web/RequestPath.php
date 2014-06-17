<?php
namespace Hyperframework\Web;

final class RequestPath {
    private static $path;
    private static $segments;

    public static function get() {
        if (self::$path === null) {
            self::$path = reset(explode('?', $_SERVER['REQUEST_URI'], 2));
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
            self::$segments = explode('/', ltrim(self::get(), '/'));
        }
        return self::$segments;
    }
}
