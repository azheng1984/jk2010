<?php
namespace Hyperframework\Web;

class ApplicationContext {
    private static $params;

    public static function get($name) {
        if (isset(self::$params[$name])) {
            return self::$params[$name];
        }
    }

    public static function getAll() {
        return self::$params;
    }

    public static function set($name, $value) {
        return self::$params[$name] = $value;
    }

    public static function has($name) {
        return isset(self::$params[$name]);
    }

    public static function remove($name) {
        unset(self::$params[$name]);
    }

    public static function reset() {
        self::$params = null;
    }
}
