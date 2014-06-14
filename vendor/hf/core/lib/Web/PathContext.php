<?php
class PathContext {
    private static $data;

    public static function get($name) {
        if (isset(self::$params[$name])) {
            return self::$params[$name];
        }
    }

    public static function set($name, $value) {
        return self::$params[$paramName] = $paramValue;
    }

    public static function getAll() {
        return self::$params;
    }

    public static function has($name) {
        return isset(self::$params[$paramName]);
    }

    public static function remove($name) {
        unset(self::$params[$paramName]);
    }
}
