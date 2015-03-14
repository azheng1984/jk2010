<?php
namespace Hyperframework\Common;

class Registry {
    private static $data = [];

    public static function get($name) {
        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
    }

    public static function set($name, $value) {
        self::$data[$name] = $value;
    }

    public static function remove($name) {
        unset(self::$data[$name]);
    }

    public static function has($name) {
        return isset(self::$data[$name]);
    }

    public static function clear() {
        self::$data = [];
    }
}
