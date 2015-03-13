<?php
namespace Hyperframework\Common;

class Registry {
    private static $objects = [];

    public static function get($key) {
        if (isset(self::$objects[$name])) {
            return self::$objects[$name];
        }
    }

    public static function set($key, $object) {
        self::$object[$key] = $object;
    }

    public static function clear() {
        self::$objects = [];
    }
}
