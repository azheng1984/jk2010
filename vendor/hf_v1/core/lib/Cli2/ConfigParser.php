<?php
namespace Hyperframework\Cli;

class ConfigParser {
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
                    'is_collection' => $isCollection
                );
            }
        } else {
            throw new Exception;
        }
    }

    public static function getDefaultArgumentConfig() {
    }

    public static function checkArgumentConfig() {
    }

    public static function parseOption($option) {
        if (strlen($option) < 2) {
            throw new Exception;
        }
        if ($option[0] !== '-') {
            throw new Exception;
        }
        $alias = null;
        if (strpos($option, ',') !== false) {
            $tmps = explode(',', $option, 2);
            $shortOption = trim($tmps[0]);
            if (strlen($shortOption) !== 2) {
                throw new Exception;
            }
            $alias = $shortOption[1];
            $option = trim($tmps[1]);
        } elseif ($option[1] !== '-') {
            if (strlen($option) > 2) {
                if ($option[2] === '[') {
                    $option = str_replace('[', '[=', 1);
                } elseif ($option[2] === '<') {
                    $option = str_replace('<', '=<', 1);
                } elseif ($option[2] === '(') {
                    $option = str_replace('(', '=(', 1);
                }
            }
        }
        if (strpos$option)
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
            $char = $option[$index];
            if ($index < 2) {
                if ($char === '-') {
                    continue;
                } else {
                    throw new Exception;
                }
            }
            if ($argumentName === null && $char !== '[' && $char !== '=') {
                $name .= $char;
                continue;
            }
            if ($argumentName === null) {
                if ($hasArgument === false) {
                    if ($char === '[') {
                        if ($option[$length - 1] !== ']') {
                            throw new Exception;
                        }
                        --$length;
                        ++$index;
                        if (isset($option[$index]) && $opiton[$index] === '=') {
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
                        if ($option[$length - 1] !== ')') {
                            throw new Exception;
                        }
                        --$length;
                        $isEnumArgument = true;
                        $argumentName = '';
                        continue;
                    }
                    if ($char === '<') {
                        if ($option[$length - 1] !== '>') {
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

    public static function parseShortOption($option) {
        $length = strlen($option);
        if ($length < 2) {
            throw new Exception;
        }
        if ($option[0] !== '-') {
            throw new Exception;
        }
        $name = $option[1];
        $index = 2;
        if ($length > 2 && $option[2] === ' ') {
            $index = 3;
        }
        $argumentName = null;
        $isOptionalArgument = false;
        $isEnumArgument = false;
        $hasArgument = false;
        for (; $index < $length; ++$index) {
            $char = $option[$index];
            if ($argumentName === null) {
                if ($hasArgument === false) {
                    if ($char === '[') {
                        if ($index !== 2 || $option[$length - 1] !== ']') {
                            throw new Exception;
                        }
                        --$length;
                        continue;
                    }
                } else {
                    if ($char === '(') {
                        if ($option[$length - 1] !== ')') {
                            throw new Exception;
                        }
                        --$length;
                        $isEnumArgument = true;
                        $argumentName = '';
                        continue;
                    }
                    if ($char === '<') {
                        if ($option[$length - 1] !== '>') {
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
        if (preg_match('/^[a-zA-Z0-9]$/', $name) !== 1) {
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
}
