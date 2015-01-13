<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class ArgumentConfigParser {
    public static function parse(array $configs) {
        $result = [];
        foreach ($configs as $config) {
            if (strpos($config, ' ') !== false) {
                throw new ConfigException(
                    self::getErrorMessage($config, 'space is not allowed.')
                );
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
                        $config , "'[' or ']' is not closed.")
                    );
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
                            . "'<' or '>' is not closed."
                    );
                }
                $name = substr($config, 1, $length - 2);
                if (preg_match('/^[a-zA-Z0-9-]+$/', $name) !== 1) {
                    throw new ConfigException(self::getErrorMessage($config)
                        . 'argument name includes invalid character.'
                    );
                } else {
                    $result[] = new ArgumentConfig(
                        $name, $isOptional, $isRepeatable
                    );
                }
            } else {
                throw new ConfigException(self::getErrorMessage(
                    $config, "argument name must be around with '<' and '>'."
                );
            }
        }
        return $result;
    }

    private static function getErrorMessage($config, $suffix = null) {
        $result = "Argument config '$config' error";
        if ($suffix === null) {
            return $result . '.';
        }
        return ', ' . $suffix;
    }
}
