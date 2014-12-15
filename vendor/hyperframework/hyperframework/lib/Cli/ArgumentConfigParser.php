<?php
namespace Hyperframework\Cli;

use Exception;

class ArgumentConfigParser {
    public static function parse($configs) {
        if (is_array($configs) === false) {
            throw new Exception;
        }
        $result = [];
        foreach ($configs as $config) {
            if (strpos($config, ' ') !== false) {
                throw new Exception;
            }
            $isOptional = false;
            $isRepeatable = false;
            $length = strlen($config);
            if ($length < 3) {
                throw new Exception;
            }
            if ($config[0] === '[') {
                $isOptional = true;
                if ($config[$length - 1] !== ']') {
                    throw new Exception;
                }
                $config = substr($config, 1, $length - 2);
                $length -= 2;
                if ($length < 3) {
                    throw new Exception;
                }
            }
            if ($config[0] === '<') {
                if (substr($config, -3) === '...') {
                    $config = substr($config, 0, $length - 3);
                    $length -= 3;
                    $isRepeatable = true;
                    if ($length < 3) {
                        throw new Exception;
                    }
                }
                if ($config[$length - 1] !== '>') {
                    throw new Exception;
                }
                $name = substr($config, 1, $length - 2);
                if (preg_match('/^[a-zA-Z0-9-]+$/', $name) !== 1) {
                    throw new Exception;
                } else {
                    $result[] = new ArgumentConfig(
                        $name, $isOptional, $isRepeatable
                    );
                }
            } else {
                throw new Exception;
            }
        }
        return $result;
    }
}
