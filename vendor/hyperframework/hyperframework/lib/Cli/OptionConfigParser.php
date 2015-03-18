<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class OptionConfigParser {
    public static function parse(array $configs) {
        $result = [];
        foreach ($configs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(
                    "Option config must be an array, $type given."
                );
            }
            $name = null;
            $shortName = null;
            $isRequired = false;
            $isRepeatable = false;
            $argumentConfig = null;
            $description = null;
            foreach ($config as $key => $value) {
                switch ($key) {
                    case 'name':
                        $name = $value;
                        break;
                    case 'short_name':
                        $shortName = $value;
                        break;
                    case 'required':
                        $isRequired = $value;
                        break;
                    case 'repeatable':
                        $isRepeatable = $value;
                        break;
                    case 'argument':
                        $argumentConfig = self::parseArgument($value);
                        break;
                    case 'description':
                        $description = $value;
                }
            }
            if ($name === null && $shortName === null) {
                throw new ConfigException(
                    "Command option config error,"
                        . " field 'name' or 'short_name'"
                        . " is required and cannot equal null."
                );
            }
            if ($name !== null) {
                if (is_string($name) === false) {
                    $type = gettype($name);
                    throw new ConfigException(
                        "Command argument config error, the value of field"
                            . " 'name' must be a string, $type given."
                    );
                }
                if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
                    throw new ConfigException(
                        "Command option config error, value '$name' "
                            . "of field 'name' is invalid."
                    );
                }
            }
            if ($shortName !== null) {
                if (is_string($shortName) === false) {
                    $type = gettype($shortName);
                    throw new ConfigException(
                        "Command argument config error, the value of field"
                            . " 'short_name' must be a string, $type given."
                    );
                }
                if (strlen($shortName) !== 1
                    || ctype_alnum($shortName) === false
                ) {
                    throw new ConfigException(
                        "Command option config error, value '$shortName' of "
                            . "field 'short_name' is invalid."
                    );
                }
            }
            if (is_bool($isRequired) === false) {
                $type = gettype($isRequired);
                throw new ConfigException(
                    "Command argument config error, the value of field"
                        . " 'required' must be a boolean, $type given."
                );
            }
            if (is_bool($isRepeatable) === false) {
                $type = gettype($isRepeatable);
                throw new ConfigException(
                    "Command argument config error, the value of field"
                        . " 'repeatable' must be a boolean, $type given."
                );
            }
            if ($description !== null) {
                if (is_string($description) === false) {
                    $type = gettype($description);
                    throw new ConfigException(
                        "Command argument config error, the value of field"
                            . " 'description' must be a string, $type given."
                    );
                }
            }
            $optionConfig = new OptionConfig(
                $name,
                $shortName,
                $isRequired,
                $isRepeatable,
                $argumentConfig,
                $description
            );
            if ($name !== null) {
                if (isset($result[$name])) {
                    throw new ConfigException(
                        "Command option config error, "
                            . "option '$name' already defined"
                    );
                }
                $result[$name] = $optionConfig;
            }
            if ($shortName !== null) {
                if (isset($result[$shortName])) {
                    throw new ConfigException(
                        "Command option config error, "
                            . "option '$shortName' already defined"
                    );
                }
                $result[$shortName] = $optionConfig;
            }
        }
        return $result;
    }

    private static function parseArgument($config) {
        if (is_array($config) === false) {
            $type = gettype($config);
            throw new ConfigException(
                "Option argument config must be an array, $type given."
            );
        }
        $name = null;
        $isRequired = true;
        $values = false;
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'name':
                    $name = $value;
                    break;
                case 'required':
                    $isRequired = $value;
                    break;
                case 'values':
                    $values = $value;
            }
        }
        if ($name === null) {
            throw new ConfigException(
                "Command option argument config error, "
                    . "field 'name' is missing or equals null."
            );
        }
        if (is_string($name) === false) {
            $type = gettype($name);
            throw new ConfigException(
                "Command option argument config error, the value of field"
                    . " 'name' must be a string, $type given."
            );
        }
        if (preg_match('/^[a-zA-Z0-9-]*$/', $name) !== 1) {
            throw new ConfigException(
                "Command option argument config error, value '$name' of "
                    . "field 'name' is invalid."
            );
        }
        if (is_bool($isRequired) === false) {
            $type = gettype($isRequired);
            throw new ConfigException(
                "Command option argument config error, the value of field"
                    . " 'required' must be a boolean, $type given."
            );
        }
        if ($values !== null) {
            if (is_array($values) === false) {
                $type = gettype($values);
                throw new ConfigException(
                    "Command option argument config error, the value of field"
                        . " 'values' must be an array , $type given."
                );
            }
            foreach ($values as &$value) {
                if (is_string($value) === false) {
                    $type = gettype($value);
                    throw new ConfigException(
                        "Command option argument config error, the element of"
                            . " field 'values' must be a string, $type given."
                    );
                }
                if (preg_match('/^[a-zA-Z0-9-_]+$/', $value) !== 1) {
                    throw new ConfigException(
                        "Command option argument config error, element '$value'"
                            . " of field 'values' is invalid."
                    );
                }
            }
            return new OptionArgumentConfig($name, $isRequired, $values);
        }
    }
}
