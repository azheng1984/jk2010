<?php
namespace Hyperframework\Common;

use Exception;

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

    public static function remove($key) {
        self::set($key, null);
    }

    public static function import($configs) {
        if ($configs === null) {
            return;
        }
        if (is_string($configs)) {
            $configs = ConfigFileLoader::loadPhp($configs);
        }
        if (is_array($configs) === false) {
            throw new Exception;
        }
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
                    $namespace = null;
                } else {
                    $namespace .= '.';
                }
                continue;
            }
            if ($namespace !== null) {
                $key = $namespace . $key;
            }
            self::$data[$key] = $value;
        }
    }

    public static function getAll() {
        return self::$data;
    }
}
