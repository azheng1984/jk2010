<?php
namespace Hyperframework;

class Config {
    private static $data = array();

    public static function get($key, $options = null) {
        $value = null;
        if (isset(static::$data[$class][$key])) {
            $value = static::$data[$class][$key];
        }
        if ($value !== null) {
            return $value;
        }
        if (isset($options['default'])) {
            return $options['default'];
        }
        if (isset($options['is_nullable']) &&
            $options['is_nullable'] === false) {
            throw new \Exception('Config \'' . $key . '\' is null');
        }
        return $value;
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

    public static function setRootPath($value) {
        static::$data['Hyperframework\RootPath'] = $value;
    }

    public static function getRootPath() {
        if (isset(static::$data['Hyperframework\RootPath']) === false)) {
            static::$data['Hyperframework\RootPath'] =
                getcwd() . DIRECTORY_SEPARATOR;
        }
        return static::$data['Hyperframework\RootPath'];
    }

    public static function setCachePath($value) {
        static::$data['Hyperframework\CachePath'] = $value;
    }

    public static function getCachePath() {
        if (isset(static::$data['Hyperframework\CachePath']) === false)) {
            $data['Hyperframework\CachePath'] =
                static::getRootPath() . 'cache' . DIRECTORY_SEPARATOR;
        }
        return static::$data['Hyperframework\CachePath'];
    }

    public static function setConfigPath($value) {
        static::$data['Hyperframework\ConfigPath'] = $value;
    }

    public static function getConfigPath() {
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
