<?php
namespace Hyperframework\Common;

class ConfigEngine {
    private $data = [];
    private $appRootPath;

    public function get($name, $default = null) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return $default;
    }

    public function getString($name, $default = null) {
        $result = $this->get($name);
        if ($result === null) {
            return $default;
        }
        if (is_string($result)) {
            return $result;
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

    public function getBoolean($name, $default = null) {
        $result = $this->get($name);
        if ($result === null) {
            return $default;
        }
        return (bool)$result;
    }

    public function getInt($name, $default = null) {
        $result = $this->get($name);
        if ($result === null) {
            return $default;
        }   
        if (is_object($result)) {
            throw new ConfigException(
                "Config '$name' requires an integer, object of class '"
                    . get_class($result)
                    . "' could not be converted to integer."
            );
        }
        return (int)$result;
    }

    public function getFloat($name, $default = null) {
        $result = $this->get($name);
        if ($result === null) {
            return $default;
        }
        if (is_object($result)) {
            throw new ConfigException(
                "Config '$name' requires a float, object of class '"
                    . get_class($result) . "' could not be converted to float."
            );
        }
        return (float)$result;
    }

    public function getArray($name, $default = null) {
        $result = $this->get($name);
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

    public function getAppRootPath() {
        $configName = 'hyperframework.app_root_path';
        $appRootPath = $this->getString($configName);
        if ($this->appRootPath === null
            || $appRootPath !== $this->appRootPath
        ) {
            if ($appRootPath === null) {
                throw new ConfigException(
                    "Config '$configName' does not exist."
                );
            }
            $isFullPath = FileFullPathRecognizer::isFullPath($appRootPath);
            if ($isFullPath === false) {
                throw new ConfigException(
                    "The value of config '$configName'"
                        . " must be a full path, '$appRootPath' given."
                );
            }
            $this->appRootPath = $appRootPath;
        }
        return $appRootPath;
    }

    public function getAppRootNamespace() {
        return $this->getString('hyperframework.app_root_namespace', '');
    }

    public function getAll() {
        return $this->data;
    }

    public function set($name, $value) {
        $name = (string)$name;
        $this->checkName($name);
        $this->data[$name] = $value;
    }

    public function has($name) {
        return isset($this->data[$name]);
    }

    public function remove($name) {
        unset($this->data[$name]);
    }

    public function import($data) {
        $namespace = null;
        foreach ($data as $name => $value) {
            if (is_int($name)) {
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
                $name = $namespace . $name;
            }
            $this->set($name, $value);
        }
    }

    public function importFile($path) {
        $data = ConfigFileLoader::loadPhp($path);
        if ($data === null) {
            return;
        }
        if (is_array($data) === false) {
            throw new ConfigException(
                "Config file '$path' must return "
                    . " an array, " . gettype($data) . ' returned.'
            );
        }
        $this->import($data);
    }

    private function checkName($name) {
        if ($name === '') {
            throw new ConfigException("Config name cannot be empty.");
        } elseif (preg_match('/^([a-zA-Z0-9_]+\.?)+$/', $name) === 0
            || substr($name, -1) === '.'
        ) {
            throw new ConfigException("Invalid config name '$name'.");
        }
    }
}
