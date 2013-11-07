<?php
namespace Hyperframework;

class Env {
    private static $data = array();

    public static function has($name) {
        return static::$data[$name];
    }

    public static function enable($name) {
        return static::$data[$name] = true;
    }

    public static function disable($name) {
        return static::$data[$name] = false;
    }

    public static function export() {
        return static::$data;
    }

    public static function reset() {
        static::$data = array();
    }
}
