<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class MutuallyExclusiveOptionGroupConfigParser {
    public static function parse(
        array $configs, array $optionConfigs, $isSubcommandEnabled, $subcommand
    ) {
        $result = [];
        $includedOptionConfigs = [];
        foreach ($configs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(self::getErrorMessage(
                    $isSubcommandEnabled,
                    $subcommand,
                    "config must be an array, $type given"
                ));
            }
            $isRequired = false;
            $mutuallyExclusiveOptionConfigs = [];
            foreach ($config as $key => $value) {
                if (is_string($key)) {
                    if ($key === 'required') {
                        $isRequired = (bool)$value;
                    }
                    continue;
                }
                if (is_string($value) === false) {
                    $type = gettype($value);
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommand,
                        "option must be a string, $type given"
                    ));
                }
                $length = strlen($value);
                if (isset($optionConfigs[$value]) === false) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommand,
                        "option '$value' is not defined"
                    ));
                }
                $optionConfig = $optionConfigs[$value];
                if (in_array(
                    $optionConfig, $mutuallyExclusiveOptionConfigs, true
                )) {
                    continue;
                }
                if (in_array($optionConfig, $includedOptionConfigs, true)) {
                    throw new ConfigException(self::getErrorMessage(
                        $isSubcommandEnabled,
                        $subcommand,
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
        $isSubcommandEnabled, $subcommand, $extra
    ) {
        if ($subcommand === null) {
            if ($isSubcommandEnabled) {
                $result = 'Command';
            } else {
                $result = 'Global command';
            }
        } else {
            $result = "Subcommand '$subcommand'";
        }
        return $result . ' mutually exclusive option group config error, '
            . $extra . '.';
    }
}
