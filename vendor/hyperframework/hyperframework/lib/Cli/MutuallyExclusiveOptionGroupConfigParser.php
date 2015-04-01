<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class MutuallyExclusiveOptionGroupConfigParser {
    public static function parse(
        $mutuallyExclusiveOptionGroupConfigs,
        $optionConfigIndex,
        $isSubcommandEnabled = false,
        $subcommandName = null
    ) {
        $result = [];
        $includedOptionConfigs = [];
        foreach ($mutuallyExclusiveOptionGroupConfigs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommandName,
                    "config must be an array, $type given"
                ));
            }
            $isRequired = false;
            $mutuallyExclusiveOptionConfigs = [];
            foreach ($config as $key => $value) {
                if (is_string($key)) {
                    if ($key === 'required') {
                        if (is_bool($value) === false) {
                            $type = gettype($value);
                            throw new ConfigException(self::getErrorMessage(
                                $isSubcommandEnabled,
                                $subcommandName,
                                "field 'required' must be a boolean, "
                                    . "$type given"
                            ));
                        }
                        $isRequired = (bool)$value;
                    }
                    continue;
                }
                if (is_string($value) === false) {
                    $type = gettype($value);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        "option must be a string, $type given"
                    ));
                }
                $length = strlen($value);
                if (isset($optionConfigIndex[$value]) === false) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        "option '$value' is not defined"
                    ));
                }
                $optionConfig = $optionConfigIndex[$value];
                if (in_array(
                    $optionConfig, $mutuallyExclusiveOptionConfigs, true
                )) {
                    continue;
                }
                if (in_array($optionConfig, $includedOptionConfigs, true)) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommandName,
                        "option '$value' cannot belong to multiple groups"
                    ));
                }
                $mutuallyExclusiveOptionConfigs[] = $optionConfig;
            }
            $result[] = new MutuallyExclusiveOptionGroupConfig(
                $mutuallyExclusiveOptionConfigs, $isRequired
            );
            $includedOptionConfigs = array_merge(
                $includedOptionConfigs, $mutuallyExclusiveOptionConfigs
            );
        }
        return $result;
    }

    private static function getErrorMessage(
        $isSubcommandEnabled, $subcommandName, $extra
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
        return $result . ' mutually exclusive option group config error, '
            . $extra . '.';
    }
}
