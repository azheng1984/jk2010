<?php
namespace Hyperframework;

final class Config {
    private static $data = array();
    private static $isConstDataSourceEnabled = false;
    private static $constPrefix;

    public static function get($name) {
        if (array_key_exists($name, self::$data)) {
            return self::$data[$name];
        }
        if (self::$isConstDataSourceEnabled === true) {
            $constName = self::$constPrefix . $name;
            if (defined($constName))  {
                return constant($constName);
            }
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

    public static function enableConstDataSource($prefix) {
        self::$isConstDataSourceEnabled = true;
        self::$constPrefix = $prefix;
    }

    public static function reset() {
        self::$data = array();
        self::$isConstDataSourceEnabled = false;
        self::$constPrefix = null;
    }
}
