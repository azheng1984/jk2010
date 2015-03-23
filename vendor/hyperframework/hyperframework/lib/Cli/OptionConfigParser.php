<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class OptionConfigParser {
    public static function parse(
        array $rawOptionConfigs,
        $isSubcommandEnabled = false,
        $subcommandName = null
    ) {
        $result = [];
        foreach ($rawOptionConfigs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(self::GetErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    null,
                    null,
                    "config must be an array, $type given"
                ));
            }
            $name = null;
            $shortName = null;
            $isRequired = false;
            $isRepeatable = false;
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
                    case 'description':
                        $description = $value;
                }
            }
            if ($name === null && $shortName === null) {
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    null,
                    null,
                    "field 'name' or 'short_name'"
                        . " is required and cannot equal null."
                ));
            }
            if ($name !== null) {
                if (is_string($name) === false) {
                    $type = gettype($name);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        null,
                        null,
                        "the value of field"
                            . " 'name' must be a string, $type given"
                    ));
                }
                if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        null,
                        null,
                        "value '$name' of field 'name' is invalid"
                    ));
                }
            }
            if ($shortName !== null) {
                if (is_string($shortName) === false) {
                    $type = gettype($shortName);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $name,
                        null,
                        "the value of field"
                            . " 'short_name' must be a string, $type given"
                    ));
                }
                if (strlen($shortName) !== 1
                    || ctype_alnum($shortName) === false
                ) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $name,
                        null,
                        "value '$shortName' of "
                            . "field 'short_name' is invalid"
                    ));
                }
            }
            if (is_bool($isRequired) === false) {
                $type = gettype($isRequired);
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    $name,
                    $shortName,
                    "the value of field"
                        . " 'required' must be a boolean, $type given"
                ));
            }
            if (is_bool($isRepeatable) === false) {
                $type = gettype($isRepeatable);
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    $name,
                    $shortName,
                    "the value of field"
                        . " 'repeatable' must be a boolean, $type given"
                ));
            }
            $argumentConfig = null;
            if (isset($config['argument'])) {
                $argumentConfig = self::parseArgumentConfig(
                    $config['argument'],
                    $isSubcommandEnabled,
                    $subcommandName,
                    $name,
                    $shortName
                );
            }
            if ($description !== null) {
                if (is_string($description) === false) {
                    $type = gettype($description);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $name,
                        $shortName,
                        "the value of field"
                            . " 'description' must be a string, $type given"
                    ));
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
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $name,
                        null,
                        "it has already been defined"
                    ));
                }
                $result[$name] = $optionConfig;
            }
            if ($shortName !== null) {
                if (isset($result[$shortName])) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        null,
                        $shortName,
                        "it has already been defined"
                    ));
                }
                $result[$shortName] = $optionConfig;
            }
        }
        return $result;
    }

    private static function parseArgumentConfig(
        $rawArgumentConfig,
        $isSubcommandEnabled,
        $subcommandName,
        $optionName,
        $optionShortName
    ) {
        if (is_array($rawArgumentConfig) === false) {
            $type = gettype($rawArgumentConfig);
            throw new ConfigException(self::getErrorMessage(
                $isSubcommandEnabled,
                $subcommandName,
                $optionName,
                $optionShortName,
                "the value of field 'argument' must be an array, $type given"
            ));
        }
        $name = null;
        $isRequired = true;
        $values = null;
        foreach ($rawArgumentConfig as $key => $value) {
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
            throw new ConfigException(self::getErrorMessage(
                $isSubcommandEnabled,
                $subcommandName,
                $optionName,
                $optionShortName,
                "option argument config field 'name' is missing or equals null"
            ));
        }
        if (is_string($name) === false) {
            $type = gettype($name);
            throw new ConfigException(self::getErrorMessage(
                $isSubcommandEnabled,
                $subcommandName,
                $optionName,
                $optionShortName,
                "the value of option argument config field"
                    . " 'name' must be a string, $type given"
            ));
        }
        if (preg_match('/^[a-zA-Z0-9-]+$/', $name) !== 1) {
            throw new ConfigException(self::getErrorMessage(
                $isSubcommandEnabled,
                $subcommandName,
                $optionName,
                $optionShortName,
                "value '$name' of option argument config"
                    . " field 'name' is invalid"
            ));
        }
        if (is_bool($isRequired) === false) {
            $type = gettype($isRequired);
            throw new ConfigException(self::getErrorMessage(
                $isSubcommandEnabled,
                $subcommandName,
                $optionName,
                $optionShortName,
                "the value of option argument config field"
                    . " 'required' must be a boolean, $type given."
            ));
        }
        if ($values !== null) {
            if (is_array($values) === false) {
                $type = gettype($values);
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    $optionName,
                    $optionShortName,
                    "the value of option argument config field"
                        . " 'values' must be an array , $type given."
                ));
            }
            foreach ($values as &$value) {
                if (is_string($value) === false) {
                    $type = gettype($value);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $optionName,
                        $optionShortName,
                        "the element of option argument config field"
                            . " 'values' must be a string, $type given."
                    ));
                }
                if (preg_match('/^[a-zA-Z0-9-_]+$/', $value) !== 1) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        $optionName,
                        $optionShortName,
                        "element '$value' of option argument config field"
                            . " 'values' is invalid."
                    ));
                }
            }
        }
        return new OptionArgumentConfig($name, $isRequired, $values);
    }

    private static function getErrorMessage(
        $isSubcommandEnabled, $subcommandName, $name, $shortName, $extra
    ) {
        if ($subcommandName === null) {
            if ($isSubcommandEnabled) {
                $result = 'Global command';
            } else {
                $result = 'Command';
            }
        } else {
            $result = "Subcommand '$subcommandName'";
        }
        $result .= ' option';
        if ($name !== null) {
            $result .= " '$name'";
        } elseif ($shortName !== null) {
            $result .= " '$shortName'";
        }
        return $result . ' config error, ' . $extra;
    }
}
