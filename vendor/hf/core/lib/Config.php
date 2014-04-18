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

    public static function remove($name) {
        return unset(self::$data[$name]);
    }

    public static function import($configs) {
        foreach ($configs as $key => $value) {
            self::$data[$key] = $value;
        }
    }

    public static function export() {
        return self::$data;
    }

    public static function reset() {
        self::$data = array();
    }
}
