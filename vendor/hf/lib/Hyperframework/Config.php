<?php
namespace Hyperframework;

class Config {
    private static $data = array();

    public static function get($name, $options = null) {
        $value = null;
        if (isset(static::$data[$name])) {
            $value = static::$data[$name];
        }
        if ($value !== null || $options === null) {
            return $value;
        }
        if (isset($options['default'])) {
            $value = $options['default'];
        }
        if ($value === null
            && isset($options['is_nullable'])
            && $options['is_nullable'] === false) {
            throw new \Exception('Config \'' . $name . '\' is null');
        }
        return $value;
    }

    public static function set(/*$mixed, ...*/) {
        $arguments = func_get_args();
        if (is_string($arguments[0])) {
            static::$data[$arguments[0]] = $arguments[1];
            return;
        }
        foreach ($arguments as $name => $item) {
            if (is_int($key)) {
                static::$data[$item[0]] = $item[1];
                return;
            }
            static::mergePrefix($key, $item);
        }
    }

    public static function export() {
        return static::$data;
    }

    public static function reset() {
        static::$data = array();
    }

    private static function getAppConst($name) {
        $namespace = static::get(__NAMESPACE__ . '\AppNamespace');
        if ($namespace === null) {
            return;
        }
        $name = $namespace . '\\' . $name;
        if (defined($name) === false) {
            return;
        }
        return constant($name);
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
