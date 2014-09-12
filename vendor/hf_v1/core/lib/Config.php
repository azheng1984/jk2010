<?php
namespace Hyperframework;

final class Config {
    private static $data = array();

    public static function get($name) {
        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
    }

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function has($name) {
        return self::get($name) !== null;
    }

    public static function remove($key) {
        self::set($key, null);
    }

    public static function import($configs) {
        foreach ($configs as $key => $value) {
            self::$data[$key] = $value;
        }
    }
}
