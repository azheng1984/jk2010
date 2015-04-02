<?php
namespace Hyperframework\Common;

class Registry {
    private static $data = [];

    /**
     * @param string $name
     * @return mixed
     */
    public static function get($name) {
        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public static function set($name, $value) {
        self::$data[$name] = $value;
    }

    /**
     * @param string $name
     */
    public static function remove($name) {
        unset(self::$data[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function has($name) {
        return isset(self::$data[$name]);
    }

    public static function clear() {
        self::$data = [];
    }
}
