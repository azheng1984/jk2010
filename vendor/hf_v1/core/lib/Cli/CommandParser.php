<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    private $elements;
    private $collectionOptions;
    private $commandName;
    private $options;
    private $arguments;

    public static function parseCommand() {
        $count = count($_SERVER['argv']);
        $options;
        $arguments;
        $isCollection;
        $argumentIndex = 0;
        for ($index = 1; $index < $count; ++$count) {
            $element = $_SERVER['argv'][$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($isCollection) {
                    if (self::hasCommand($element) === false) {
                        throw new Exception;
                    }
                    list($commandOptions, $arguments) =
                        self::getCommandConfig($element);
                    $isCollection = false;
                    $options += $commandOptions;
                    continue;
                }
                $argCount = count($arguments);
                if ($argCount > $argumentIndex) {
                    if ($arguments[$argumentIndex]['is_collection']) {
                        $result['arguments'][] = array($element);
                    }
                    $result['argument'][] = $element;
                } else {
                    $lastArg = $arguments[$argCount - 1];
                    if ($lastArg['is_collection']) {
                        $result['arguments'][$argCount - 1][] = $element;
                    } else {
                        throw new Exception;
                    }
                }
                ++$argumentIndex;
                continue;
            }
            if ($element === '--') {
                if ($isCollection) {
                    throw new Exception;
                }
                $isArgument = true;
                continue;
            }
            if ($element[1] !== '-') {
                $i = 1;
                while ($length >= 2) {
                    $optionName = $element[$i];
                    if (isset($options[$optionName]) === false) {
                        throw new Exception;
                    }
                    $optionArg = true;
                    $option = $options[$optionName];
                    if ($option['has_argument'] === 0) {
                        if ($length > 2) {
                            $optionArg = substr($element, 1 + $i);
                        }
                    } elseif ($option['has_argument'] === 1) {
                        if ($length > 2) {
                            $optionArg = substr($element, 1 + $i);
                        } else {
                            ++$index;
                            if ($index >= $count) {
                                throw new Exception;
                            }
                            $optionArg = $_SERVER['argv'][$index];
                        }
                    }
                    foreach ($option['mutex_options'] as $item) {
                        if (isset($result['options'][$item])) {
                            throw new Exception;
                        }
                    }
                    if ($option['is_repeatable']) {
                        if (isset($result['options'][$optionName])) {
                            $result['options'][$optionName][] = $optionArg;
                        } else {
                            $result['options'][$optionName] = array($optionArg);
                        }
                    }
                    $result['options'][$optionName] = $optionArg;
                    if ($optionArg !== true) {
                        break;
                    }
                    ++$i;
                    --$length;
                }
            } else {
                $optionArg = true;
                $optionName = substr($element[0], 2);
                if (strpos($element, '=') !== false) {
                    list($optionName, $optionArg) = explode('=', $element, 2);
                }
                if (isset($options[$optionName])) {
                    throw new Exception;
                }
                $option = $options[$optionName];
                if ($option['has_argument'] === 0) {
                    $result['options'][] = array($optionName, $optionArg);
                } elseif ($option['has_argument'] === 1) {
                    if ($optionArg === null) {
                        ++$index;
                        if ($index >= $count) {
                            throw new Exception;
                        }
                        $optionArg = $_SERVER['argv'][$index];
                    }
                    $result['options'][$optionName] = $optionArg;
                } else {
                    if ($optionArg !== null) {
                        throw new Exception;
                    }
                }
            }
        }
        foreach ($requiredOptions as $index => $item) {
            if (is_array($item)) {
                foreach ($item as $option) {
                    if (isset($result['options'][$option]) === false) {
                        unset($requiredOptions[$index]);
                        continue 2;
                    }
                }
            } elseif (isset($result['options'][$item]) === false) {
                unset($requiredOptions[$index]);
                continue;
            }
            throw new Exception;
        }
        if ($argIndex < self::getRequiredArgumentNumber()) {
            throw new Exception;
        }
    }

    public static function parseArgument($config) {
        $isOptional = false;
        $isCollection = false;
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
                $isCollection = true;
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
                return array(
                    'name' => $name,
                    'is_optional' => $isOptional,
                    'is_collection' => $isCollection;
                );
            }
        } else {
            throw new Exception;
        }
    }

    public static function parseLongOption($option) {
        $length = strlen($option);
        if ($length < 4) {
            throw new Exception;
        }
        $name = null;
        $argumentName = null;
        $isOptionalArgument = false;
        $isEnumArgument = false;
        $hasArgument = false;
        for ($index = 0; $index < $length; ++$index) {
            $char = $item[2][$index];
            if ($index < 2) {
                if ($char === '-') {
                    continue;
                } else {
                    throw new Exception;
                }
            }
            if ($argumentName === null 
                && $char !== '['
                && $char !== '='
            ) {
                $name .= $char;
                continue;
            }
            if ($argumentName === null) {
                if ($hasArgument === false) {
                    if ($char === '[') {
                        if ($item[2][$length - 1] !== ']') {
                            throw new Exception;
                        }
                        --$length;
                        ++$index;
                        if (isset($item[2][$index])
                            && $item[2][$index] === '='
                        ) {
                            $isOptionalArgument = true;
                        } else {
                            throw new Exception;
                        }
                        $hasArgument = true;
                        continue;
                    }
                    if ($char === '=' || $char === ' ') {
                        $hasArgument = true;
                        continue;
                    }
                } else {
                    if ($char === '(') {
                        if ($item[2][$length - 1] !== ')') {
                            throw new Exception;
                        }
                        --$length;
                        $isEnumArgument = true;
                        $argumentName = '';
                        continue;
                    }
                    if ($char === '<') {
                        if ($item[2][$length - 1] !== '>') {
                            throw new Exception;
                        }
                        --$length;
                        $argumentName = '';
                        continue;
                    }
                }
                throw new Exception;
            }
            $argumentName .= $char;
        }
        if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
            throw new Exception;
        }
        $result = array(
            'name' => $name,
            'has_argument' => -1
        );
        if ($hasArgument) {
            if ($isEnumArgument) {
                $result['argument_values'] = array();
                if (preg_match('/^[a-zA-Z0-9-|]+$/', $argumentName) !== 1) {
                    throw new Exception;
                }
                $result['argument_values'] = explode('|', $argumentName);
            } elseif (preg_match('/^[a-zA-Z0-9-]{2,}$/', $argumentName) !== 1) {
                throw new Exception;
            }
            if ($isOptoinalArgument) {
                $result['has_argument'] = 0;
            } else {
                $result['has_argument'] = 1;
            }
        }
        return $result;
    }

    public static function parseShortOption() {
        //reuse long option parser
    }

    public static function run($config, $isCollection) {
        $options = array();
        if (isset($config['options'])) {
            if (is_array($config['options']) === false) {
                throw new Exception;
            }
            foreach ($options as $key => $value) {
                if (is_int($key)) {
                    if (is_array($value)) {//group
                        continue;
                    } else {//opiton only
                        $key = $value;
                        $value = null;
                    }
                }
                $shortOption;
                $longOption;
                if (strpos($key, ',') !== false) {
                    $items = explode(',', $key);
                    if (count($items) !== 2) {
                        throw new Exception;
                    }
                    if (strlen($item[0]) !== 2 && $items[0][0] !== '-') {
                        throw new Exception;
                    }
                    if (ctype_alnum($item[0][1]) === false) {
                        throw new Exception;
                    }
                    $shortOption = $item[0][1];
                    $longOption = ltrim($item[1]);
                } else {
                }
            }
        }
    }

    //用于继承 +  construct 自动处理部分选项
    public static function getOptions() {
    }

    //过滤器自动验证, give argument a name
    public static function getArguments() {
    }

    //通过过滤器处理时用于区分
    public static function getCommandName() {
    }
}
