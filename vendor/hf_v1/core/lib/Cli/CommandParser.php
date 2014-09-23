<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    public static function parseUsage() {
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
                $argumentName = str_replace(' | ', '|', $argumentName);
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

    public static function parseShortOption($option) {
        $length = strlen($option);
        if ($length < 2 || $option[0] !== '-') {
            throw new Exception;
        }
        $name = $option[1];
        $argumentName = null;
        $isOptionalArgument = false;
        $isEnumArgument = false;
        $hasArgument = false;
        for ($index = 2; $index < $length; ++$index) {
            $char = $option[$index];
            if ($argumentName === null) {
                if ($hasArgument === false) {
                    if ($char === ' ') {
                        continue;
                    }
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
        if (ctype_alnum($name) === false) {
            throw new Exception;
        }
        $result = array(
            'name' => $name,
            'has_argument' => $hasArgument
        );
        if ($hasArgument) {
            if (preg_match('/^[a-zA-Z0-9-]+$/', $argumentName) !== 1) {
                throw new Exception;
            }
            $result['is_enum_argument'] = $isEnumArgument;
            $result['is_optional_argument'] = $isOptoinalArgument;
        }
        return $result;
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
                $key = str_replace(', ', '', $key);
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
                } else {
                }
            }
        }
        $argv = $_SERVER['argv'];
    //  print_r($config);
        print_r($_SERVER);
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

    public static function getUsageName() {
    }
}
