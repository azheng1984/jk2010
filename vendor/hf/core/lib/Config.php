<?php
namespace Hyperframework;

final class Config {
    private static $applicationPath;
    private static $data = array();

    public static function initailize($applicationNamespace) {
        self::$data[__NAMESPACE__ . '\ApplicationNamespace']
            = $applicationNamespace;
    }

    public static function get($name, $options = null) {
        $value = null;
        if (isset(self::$data[$name])) {
            $value = self::$data[$name];
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

    public static function getApplicationPath() {
        if (self::$applicationPath === null) {
            self::$applicationPath = self::get(
                __NAMESPACE__ . '\ApplicationPath',
                array(
                    'default' => array('application_const' => 'ROOT_PATH'),
                    'is_nullable' => false
                )
            );
        }
        return self::$applicationPath;
    }

    public static function set(/*$mixed, ...*/) {
        $arguments = func_get_args();
        if (is_string($arguments[0])) {
            self::$data[$arguments[0]] = $arguments[1];
            return;
        }
        foreach ($arguments as $name => $item) {
            if (is_int($key)) {
                self::$data[$item[0]] = $item[1];
                return;
            }
            self::mergePrefix($key, $item);
        }
    }

    public static function export() {
        return self::$data;
    }

    public static function reset() {
        self::$data = array();
    }

    private static function getApplicationConst($name) {
        $namespace = static::get(__NAMESPACE__ . '\ApplicationNamespace');
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
            self::$data[$prefix . '\\' . $data[0]] = $data[1];
            return;
        }
        foreach ($data as $key => $item) {
            if (is_int($key)) {
                self::$data[$prefix . '\\' . $item[0]] = $item[1];
                continue;
            }
            static::mergePrefix($prefix . '\\' . $key, $item);
        }
    }
}
