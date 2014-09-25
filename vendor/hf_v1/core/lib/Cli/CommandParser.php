<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    public static function parseCommand() {
        $count = count($_SERVER['argv']);
        $options;
        $arguments;
        $isArgument = false;
        for ($index = 1; $index < $count; ++$count) {
            $element = $_SERVER['argv'][$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
            }
            if ($element === '--') {
                $isArgument = true;
                continue;
            }
            if ($element[1] !== '-') {
                //short
                $option = $element[1];
                if (isset($options[$option]) === false) {
                    throw new Exception;
                }
            } else {
                //long
            }
        }
    }

    public static function parseArgument($config) {
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
                return array(
                    'name' => $name,
                    'is_optional' => $isOptional,
                    'is_repeatable' => $isRepeatable
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

    public static function getOptions() {
    }

    public static function getArguments() {
    }

    public static function getElements() {
    }

    public static function getCollectionOptions() {
    }

    public static function getCommandName() {
    }
}
