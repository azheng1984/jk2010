<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class ArgumentConfigParser {
    public static function parse(array $configs) {
        $result = [];
        $hasRepeatableArgument = false;
        $optionalArgumentName = null;
        foreach ($configs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(
                    "Argument config must be an array, $type given."
                );
            }
            $isRequired = true;
            $isRepeatable = false;
            $name = null;
            foreach ($config as $key => $value) {
                switch ($key) {
                    case 'name':
                        $name = $value;
                        break;
                    case 'required':
                        $isRequired = $value;
                        break;
                    case 'repeatable':
                        $isRepeatable = $value;
                }
            }
            if ($name === null) {
                throw new ConfigException(
                    "Command argument config error, "
                        . "field 'name' is missing or equals null."
                );
            }
            if (is_string($name) === false) {
                $type = gettype($name);
                throw new ConfigException(
                    "Command argument config error, the value of field"
                        . " 'name' must be a string, $type given."
                );
            }
            if (preg_match('/^[a-zA-Z0-9-]*$/', $name) !== 1) {
                throw new ConfigException(
                    "Command argument config error, value '$name' "
                        . "field 'name' is invalid."
                );
            }
            if (is_bool($isRequired) === false) {
                $type = gettype($isRequired);
                throw new ConfigException(
                    "Command argument config error, the value of field"
                        . " 'required' must be a boolean, $type given."
                );
            }
            if ($optionalArgumentName !== null) {
                if ($isRequired) {
                    throw new ConfigException(
                        "Command argument config error, argument '"
                            . "$optionalArgumentName' cannot be optional."
                    );
                }
            }
            if ($isRequired === false) {
                $optionalArgumentName = $name;
            }
            if (is_bool($isRepeatable) === false) {
                $type = gettype($isRepeatable);
                throw new ConfigException(
                    "Command argument config error, the value of field"
                        . " 'repeatable' must be a boolean, $type given."
                );
            }
            if ($hasRepeatableArgument) {
                throw new ConfigException(
                    "Command argument config error, "
                        . "repeatable argument must be the last one."
                );
            }
            $hasRepeatableArgument = $isRepeatable;
            $result[] = new ArgumentConfig($name, $isRequired, $isRepeatable);
        }
        return $result;
    }
}
