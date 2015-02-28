<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class ArgumentConfigParser {
    public static function parse(array $configs) {
        $result = [];
        foreach ($configs as $config) {
            if (strpos($config, ' ') !== false
                || strpos($config, "\t") !== false
            ) {
                throw new ConfigException(self::getErrorMessage(
                    $config, 'white-space character is not allowed.'
                ));
            }
            $isOptional = false;
            $isRepeatable = false;
            $length = strlen($config);
            if ($length < 3) {
                throw new ConfigException(self::getErrorMessage($config));
            }
            if ($config[0] === '[') {
                $isOptional = true;
                if ($config[$length - 1] !== ']') {
                    throw new ConfigException(self::getErrorMessage(
                        $config , "'[' must be closed by ']'."
                    ));
                }
                $config = substr($config, 1, $length - 2);
                $length -= 2;
                if ($length < 3) {
                    throw new ConfigException(self::getErrorMessage($config));
                }
            }
            if ($config[0] === '<') {
                if (substr($config, -3) === '...') {
                    $config = substr($config, 0, $length - 3);
                    $length -= 3;
                    $isRepeatable = true;
                    if ($length < 3) {
                        throw new ConfigException(
                            self::getErrorMessage($config)
                        );
                    }
                }
                if ($config[$length - 1] !== '>') {
                    throw new ConfigException(
                        self::getErrorMessage($config)
                            . "'<' must be closed by '>'."
                    );
                }
                $name = substr($config, 1, $length - 2);
                if (preg_match('/^[a-zA-Z0-9-]+$/', $name) !== 1) {
                    throw new ConfigException(
                        self::getErrorMessage($config)
                            . "argument name '$name' "
                            . "contains invalid characters."
                    );
                } else {
                    $result[] = new ArgumentConfig(
                        $name, $isOptional, $isRepeatable
                    );
                }
            } else {
                throw new ConfigException(self::getErrorMessage(
                    $config,
                    "argument name must be surrounded by '<' and '>'."
                ));
            }
        }
        return $result;
    }

    private static function getErrorMessage($config, $extra = null) {
        $result = "Argument config '$config' is invalid";
        if ($extra === null) {
            return $result . '.';
        }
        return ', ' . $extra;
    }
}
