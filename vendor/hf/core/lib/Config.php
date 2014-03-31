<?php
namespace Hyperframework;

final class Config {
    private static $data = array();

    public static function get($name, $options = null) {
        $result = null;
        if (isset(self::$data[$name])) {
            $result = self::$data[$name];
        } elseif (isset($options['default'])) {
            if (is_callable($options['default'])) {
                $callback = $options['default'];
                $result = $callback();
            } else {
                $result = $options['default'];
            }
        }
        if ($result !== null) {
            return $result ;
        }
        if (isset($options['is_nullable'])
            && $options['is_nullable'] === false) {
            throw new \Exception('Config \'' . $name . '\' is null');
        }
        return $result;
    }

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function merge($configs) {
        foreach ($configs as $key => $value) {
            self::$data[$key] = $value;
        }
    }

    public static function remove($name) {
        return unset(self::$data[$name]);
    }

    public static function export() {
        return self::$data;
    }

    public static function reset() {
        self::$data = array();
    }
}
