<?php
namespace Hyperframework\Common;

use Exception;

class Config {
    private static $data = [];

    public static function get($name, $default = null) {
        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
        return $default;
    }

    public static function getString($name, $default = null) {
        $result = null;
        if (isset(self::$data[$name])) {
            $result = self::$data[$name];
        }
        if ($result === null) {
            return $default;
        }
        if (is_scalar($result) || is_resource($result)) {
            return (string)$result;
        }
        if (is_object($result)) {
            if (method_exists($result, '__toString')) {
                return (string)$result;
            }
            throw new Exception(
                "Config '$name' requires a string. Object of class "
                    . get_class($result) . " could not be converted to string"
            );
        }
        throw new Exception(
            "Config '$name' requires a string. "
                . ucfirst(gettype($result)) . ' given'
        );
    }

    public static function getBoolean($name, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        return (bool)$result;
    }

    public static function getInt($name, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        if (is_object($result)) {
            throw new Exception(
                "Config '$name' requires an integer. Object of class "
                    . get_class($result) . " could not be converted to integer"
            );
        }
        return (int)$result;
    }

    public static function getFloat($name, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        if (is_object($result)) {
            throw new Exception(
                "Config '$name' requires a float. Object of class "
                    . get_class($result) . " could not be converted to float"
            );
        }
        return (float)$result;
    }

    public static function getArray($name, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        if (is_array($result) === false) {
            throw new Exception(
                "Config '$name' requires an array. "
                    . ucfirst(gettype($result)) . " given"
            );
        }
        return $result;
    }

    public static function getObject($name, $class = null, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        if ($class === null) {
            if (is_object($result) === false) {
                throw new Exception(
                    "Config '$name' requires an object of class. "
                        . ucfirst(gettype($result)) . " given"
                );
            }
        } elseif ($result instanceof $class === false) {
            throw new Exception(
                "Config '$name' requires an object of class '$class'. "
                    . "Object of class '". get_class($result) . "' given"
            );
        }
        return $result;
    }

    public static function getResource($name, $default = null) {
        $result = static::get($name);
        if ($result === null) {
            return $default;
        }
        if (is_resource($result) === false) {
            throw new Exception(
                "Config '$name' requires a resource. "
                    . ucfirst(gettype($result)) . ' given'
            );
        }
        return $result;
    }

    public static function set($key, $value) {
        self::$data[$key] = $value;
    }

    public static function has($key) {
        return isset(self::$data[$key]);
    }

    public static function remove($key) {
        unset(self::$data[$key]);
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
