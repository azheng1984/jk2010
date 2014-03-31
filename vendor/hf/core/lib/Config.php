<?php
namespace Hyperframework;

final class Config {
    private static $data = array();

    public static function initailize($applicationNamespace) {
        self::$data[__NAMESPACE__ . '.application_namespace']
            = $applicationNamespace;
    }

    public static function get($name, $options = null) {
        $value = null;
        if (isset(self::$data[$name])) {
            $value = self::$data[$name];
        } elseif (isset($options['default'])) {
            if (is_callable($options['default'])) {
                $callback = $options['default'];
                $value = $callback();
            } else {
                $value = $options['default'];
            }
        }
        if ($value !== null || $options === null) {
            return $value;
        }
        if ($value === null
            && isset($options['is_nullable'])
            && $options['is_nullable'] === false) {
            throw new \Exception('Config \'' . $name . '\' is null');
        }
        return $value;
    }

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function merge($configs) {
        foreach ($configs as $key => $value) {
            self::$data[$key] = $value;
        }
    }

    public static function delete($name) {
        return unset(self::$data[$name]);
    }

    public static function export() {
        return self::$data;
    }

    public static function reset() {
        self::$data = array();
    }
}
