<?php
namespace Hyperframework;

class EnvConfig {
    private static $data = array();

    public static function hasEnv($name) {
        return static::$data[$name];
    }

    public static function enableEnv($name) {
        return static::$data[$name] = true;
    }

    public static function disableEnv($name) {
        return static::$data[$name] = false;
    }

    public static function export() {
        return static::$data;
    }

    public static function reset() {
        static::$data = array();
    }
}
