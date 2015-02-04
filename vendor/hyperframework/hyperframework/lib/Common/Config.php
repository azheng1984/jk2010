<?php
namespace Hyperframework\Common;

class Config {
    private static $data = [];
    private static $appRootPath;

    public static function get($name, $default = null) {
        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
        return $default;
    }

    public static function getString($name, $default = null) {
        $result = static::get($name);
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
                . gettype($result) . ' could not be converted to string.'
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

    public static function getObject($name, $default = null) {
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
        if (isset(self::$appRootPath) === false) {
            self::$appRootPath = static::getString(
                'hyperframework.app_root_path'
            );
            if (self::$appRootPath === null) {
                throw new ConfigException(
                    "Config 'hyperframework.app_root_path' does not exist."
                );
            }
            $isFullPath = FullPathRecognizer::isFull(
                self::$appRootPath
            );
            if ($isFullPath === false) {
                throw new ConfigException(
                    "The value of config 'hyperframework.app_root_path'"
                        . " must be a full path, '"
                        . self::$appRootPath . "' given."
                );
            }
        }
        return self::$appRootPath;
    }

    public static function getAppRootNamespace() {
        return static::getString('hyperframework.app_root_namespace', '');
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

    public static function import(array $data) {
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
            static::set($key, $value);
        }
    }

    public static function importFile($path) {
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
        static::import($data);
    }

    public static function getAll() {
        return self::$data;
    }

    public static function clear() {
        self::$data = [];
        self::$appRootPath = null;
    }

    private static function checkKey($key) {
        if ($key === '') {
            throw new ConfigException("Config key cannot be empty.");
        } elseif ($key === 'hyperframework.app_root_path') {
            self::$appRootPath = null;
        } elseif (preg_match('/^([a-zA-Z0-9_]+\.?)+$/', $key) === 0
            || substr($key, -1) === '.'
        ) {
            throw new ConfigException("Invalid config key '$key'.");
        }
    }
}
