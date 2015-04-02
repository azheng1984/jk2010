<?php
namespace Hyperframework\Common;

class ConfigEngine {
    private $data = [];
    private $appRootPath;

    /**
     * @param string $name
     * @param mixed $default
     */
    public function get($name, $default = null) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return $default;
    }

    /**
     * @param string $name
     * @param string $default
     */
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

    /**
     * @param string $name
     * @param bool $default
     */
    public function getBool($name, $default = null) {
        $result = $this->get($name);
        if ($result === null) {
            return $default;
        }
        return (bool)$result;
    }

    /**
     * @param string $name
     * @param int $default
     */
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

    /**
     * @param string $name
     * @param float $default
     */
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

    /**
     * @param string $name
     * @param array $default
     */
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

    /**
     * @return string
     */
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

    /**
     * @return string
     */
    public function getAppRootNamespace() {
        return $this->getString('hyperframework.app_root_namespace', '');
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->data;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value) {
        $name = (string)$name;
        $this->checkName($name);
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name) {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     */
    public function remove($name) {
        unset($this->data[$name]);
    }

    /**
     * @param string[] $data
     */
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

    /**
     * @param string $path
     */
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

    /**
     * @param string $name
     */
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
