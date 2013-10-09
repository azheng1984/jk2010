<?php
namespace Hyperframework;

class Config {
    private static $data = array();

    public static function get($key, $default = null) {
        if (isset(static::$data[$class][$key])) {
            return static::$data[$class][$key];
        }
        return $default;
    }

    public static function set(/*$mixed, ...*/) {
        $arguments = func_get_args();
        if (is_string($arguments[0])) {
            static::$data[$arguments[0]] = $arguments[1];
            return;
        }
        foreach ($arguments as $key => $item) {
            if (is_int($key)) {
                static::$data[$item[0]] = $item[1];
                return;
            }
            static::mergePrefix($key, $item);
        }
    }

    public static function load($path) {
    }

    public static function reset() {
        static::$data = array();
    }

    private static function mergePrefix($prefix, $data) {
        if (is_string($data[0])) {
            static::$data[$prefix . '\\' . $data[0]] = $data[1];
            return;
        }
        foreach ($data as $key => $item) {
            if (is_int($key)) {
                static::$data[$prefix . '\\' . $item[0]] = $item[1];
                continue;
            }
            static::mergePrefix($prefix . '\\' . $key, $item);
        }
    }
}
