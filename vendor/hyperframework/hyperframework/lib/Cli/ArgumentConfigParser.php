<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class ArgumentConfigParser {
    public static function parse(array $configs) {
        $result = [];
        foreach ($configs as $config) {
            if (strpos($config, ' ') !== false) {
                throw new ConfigException(
                    self::getErrorMessage($config) . '(不允许存在空格)'
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
                    throw new ConfigException(
                        self::getErrorMessage($config) . '(方括号没有关闭)'
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
                        throw new ConfigException(self::getErrorMessage($config));
                    }
                }
                if ($config[$length - 1] !== '>') {
                    throw new ConfigException(
                        self::getErrorMessage($config) . '(尖括号没有关闭)'
                    );
                }
                $name = substr($config, 1, $length - 2);
                if (preg_match('/^[a-zA-Z0-9-]+$/', $name) !== 1) {
                    throw new ConfigException(self::getErrorMessage($config)
                        . '(参数名称包含非法字符)'
                    );
                } else {
                    $result[] = new ArgumentConfig(
                        $name, $isOptional, $isRepeatable
                    );
                }
            } else {
                throw new ConfigException(self::getErrorMessage($config)
                    . '(参数名称需要使用尖括号包围)'
                );
            }
        }
        return $result;
    }

    private static function getErrorMessage($config) {
        return "Argument config '$config' 格式错误";
    }
}
