<?php
namespace Hyperframework;

final class Config {
    private static $data = array();
    private static $externalDataSource;

    public static function get($name) {
        if (array_key_exists($name, self::$data)) {
            return self::$data[$name];
        }
        if (self::$externalDataSource !== null) {
            return self::$externalDataSource::get($name);
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

    public static function setExternalDataSource($instance) {
        self::$externalDataSource = $instance;
    }

    public static function reset() {
        self::$data = array();
        self::$externalDataSource = null;
    }
}
