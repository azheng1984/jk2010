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
        $namespace = null;
        foreach ($configs as $key => $value) {
            if (is_int($key)) {
                $length = strlen($value);
                if ($length === 0
                    || $value[0] !== '['
                    || $value[$length - 1] !== ']'
                ) {
                    throw new Exception;
                }
                $namespace = substr($value, 1, $length - 2);
                if ($namespace === '') {
                    throw new Exception;
                }
                continue;
            }
            if ($namespace !== null) {
                $key = $namespace . '.' . $key;
            }
            self::$data[$key] = $value;
        }
    }
}
