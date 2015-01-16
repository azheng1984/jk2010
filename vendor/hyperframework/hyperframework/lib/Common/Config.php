<?php
namespace Hyperframework\Common;

use InvalidArgumentException;

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
            throw new ConfigException(
                "Config '$name' requires a string, object of class "
                    . get_class($result) . " could not be converted to string."
            );
        }
        throw new ConfigException(
            "Config '$name' requires a string, "
                . gettype($result) . ' given.'
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
            throw new ConfigException(
                "Config '$name' requires an integer, object of class "
                    . get_class($result) . " could not be converted to integer."
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
            throw new ConfigException(
                "Config '$name' requires a float, object of class "
                    . get_class($result) . " could not be converted to float."
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
            throw new ConfigException(
                "Config '$name' requires an array, "
                    . gettype($result) . " given."
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
                throw new ConfigException(
                    "Config '$name' requires an object of class, "
                        . gettype($result) . " given."
                );
            }
        } elseif ($result instanceof $class === false) {
            throw new ConfigException(
                "Config '$name' requires an object of class '$class', "
                    . "object of class '". get_class($result) . "' given."
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
            throw new ConfigException(
                "Config '$name' requires a resource, "
                    . gettype($result) . ' given.'
            );
        }
        return $result;
    }

    public static function getAppRootPath() {
        if (isset(self::$data[':app_root_path']) === false) {
            self::$data[':app_root_path'] = Config::getString(
                'hyperframework.app_root_path'
            );
            if (self::$data[':app_root_path'] === null) {
                throw new ConfigException(
                    "Config 'hyperframework.app_root_path' is not defined."
                );
            }
            $isFullPath =
                FullPathRecognizer::isFull(self::$data[':app_root_path']);
            if ($isFullPath === false) {
                throw new ConfigException(
                    "The value of config 'hyperframework.app_root_path'"
                        . ' must be a full path, '
                        . self::$data[':app_root_path'] . ' given.'
                );
            }
        }
        return self::$data[':app_root_path'];
    }

    public static function getAppRootNamespace() {
        return Config::getString(
            'hyperframework.app_root_namespace', ''
        );
    }

    public static function set($key, $value) {
        $key = (string)$key;
        self::checkKey($key);
        self::$data[$key] = $value;
    }

    public static function has($key) {
        return isset(self::$data[$key]);
    }

    public static function remove($key) {
        unset(self::$data[$key]);
    }

    public static function import($data) {
        if (is_string($data)) {
            $path = $data;
            $data = ConfigFileLoader::loadPhp($path);
            if ($data === null) {
                return;
            }
            if (is_array($data) === false) {
                throw new ConfigException(
                    "Config file $path must return "
                        . " an array, " . gettype($data) . ' returned.'
                );
            }
        } elseif (is_array($data) === false) {
            throw new InvalidArgumentException(
                "Argument 'data' must be an array or a string of file path, "
                    . gettype($data) . ' given.'
            );
        }
        $namespace = null;
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $length = strlen($value);
                if ($length === 0
                    || $value[0] !== '['
                    || $value[$length - 1] !== ']'
                ) {
                    throw new ConfigException(
                        "Invalid config section '$value'."
                    );
                }
                $namespace = substr($value, 1, $length - 2);
                if ($namespace === '') {
                    $namespace = null;
                } else {
                    $pattern = '/^([a-zA-Z0-9_]+\.?)+$/';
                    if (preg_match($pattern, $namespace) === 0
                        || substr($namespace, -1) === '.'
                    ) {
                        throw new ConfigException(
                            "Invalid config section '$value'."
                        );
                    }
                    $namespace .= '.';
                }
                continue;
            }
            if ($namespace !== null) {
                $key = $namespace . $key;
            }
            self::checkKey($key);
            self::$data[$key] = $value;
        }
    }

    private static function checkKey($key) {
        if ($key === '') {
            throw new ConfigException("Config key cannot be empty.");
        }
        if (preg_match('/^([a-zA-Z0-9_]+\.?)+$/', $key) === 0
            || substr($key, -1) === '.'
        ) {
            throw new ConfigException("Invalid config key '$key'.");
        }
    }

    public static function getAll() {
        return self::$data;
    }
}
