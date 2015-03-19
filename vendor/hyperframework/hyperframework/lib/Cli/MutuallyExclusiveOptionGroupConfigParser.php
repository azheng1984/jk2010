<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class MutuallyExclusiveOptionGroupConfigParser {
    public static function parse(array $configs, array $options, $subcommand) {
        $result = [];
        $includedOptions = [];
        foreach ($configs as $config) {
            if (is_array($config) === false) {
                $type = gettype($config);
                throw new ConfigException(self::getErrorMessage(
                    $subcommand, "config must be an array, $type given"
                ));
            }
            $isRequired = false;
            $mutuallyExclusiveOptions = [];
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
                        $subcommand, "option must be a string, $type given"
                    ));
                }
                $length = strlen($value);
                if (isset($options[$value]) === false) {
                    throw new ConfigException(self::getErrorMessage(
                        $subcommand, "option '$value' is not defined"
                    ));
                }
                $option = $options[$value];
                if (in_array($option, $includedOptions, true)) {
                    throw new ConfigException(self::getErrorMessage(
                        $subcommand,
                        "option '$value' cannot belong to multiple groups"
                    ));
                }
                if (in_array($option, $mutuallyExclusiveOptions, true)) {
                    continue;
                }
                $mutuallyExclusiveOptions[] = $option;
            }
            if (count($mutuallyExclusiveOptions) !== 0) {
                $result[] = new MutuallyExclusiveOptionGroupConfig(
                    $mutuallyExclusiveOptions, $isRequired
                );
                $includedOptions =
                    array_merge($includedOptions, $mutuallyExclusiveOptions);
            }
        }
        return $result;
    }

    private static function getErrorMessage($subcommand, $extra) {
        if ($subcommand === null) {
            $result = 'Command';
        } else {
            $result = "Subcommand '$subcommand'";
        }
        return $result . ' mutually exclusive option group config error, '
            . $extra . '.';
    }
}
