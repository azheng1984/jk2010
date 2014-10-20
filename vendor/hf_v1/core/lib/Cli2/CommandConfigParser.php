<?php
namespace Hyperframework\Cli;

class CommandConfigParser {
    public static function getOptions($config) {
        if (isset($config['options'])) {
            if (is_array($config['options']) === false) {
                $config['options'] = array($config['options']);
            }
            foreach ($options as $key => $value) {
                if (is_int($key)) {
                    if (is_array($value)) {//group
                        continue;
                    } else {
                        $key = $value;
                        $value = array();
                    }
                }
                static::parseOptionKey($key);
            }
        }
        return array();
    }

    private static function parseOptions($config, $attributes = null) {
    }

    public static function getArguments($config) {
        if (isset($config['arguments'])) {
            if (is_array($config['arguments']) === false) {
                $config['arguments'] = array($config['arguments']);
            }
            $result = array();
            foreach ($config['arguments'] as $argumentConfig) {
                $result[] = static::parseArgument($argumentConfig);
            }
            return $result;
        } else {
            return array();
        }
    }

    protected static function parseArgument($config) {
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

    protected static function parseOptionKey($optionKey) {
        $length = strlen($optionKey);
        if ($length < 2) {
            throw new Exception;
        }
        if ($optionKey[0] !== '-') {
            throw new Exception;
        }
        $alias = null;
        $isShort = false;
        if (strpos($optionKey, ',') !== false) {
            $tmps = explode(',', $optionKey, 2);
            $shortOption = $tmps[0];
            if (strlen($shortOption) !== 2) {
                throw new Exception;
            }
            $alias = $shortOption[1];
            if (ctype_alnum($alias) === false) {
                throw new Exception;
            }
            $optionKey = ltrim($tmps[1]);
        } elseif ($optionKey[1] !== '-') {
            $isShort = true;
            if ($isShort === false) {
            }
            $optionKey = '-' . $optionKey;
            ++$length;
            if ($length > 2) {
                if ($optionKey[2] === '[') {
                    $optionKey = str_replace('[', '[=', 1);
                    ++$length;
                } elseif ($optionKey[2] === '<') {
                    $optionKey = str_replace('<', ' <', 1);
                    ++$length;
                } elseif ($optionKey[2] === '(') {
                    $optionKey = str_replace('(', ' (', 1);
                    ++$length;
                }
            }
        }
        $name = null;
        $argumentName = null;
        $isOptionalArgument = false;
        $isEnumArgument = false;
        $hasArgument = false;
        for ($index = 2; $index < $length; ++$index) {
            $char = $optionKey[$index];
            if ($argumentName === null
                && $char !== '['
                && $char !== '='
                && $char !== ' '
            ) {
                $name .= $char;
                continue;
            }
            if ($argumentName === null) {
                if ($hasArgument === false) {
                    if ($char === '[') {
                        if ($optionKey[$length - 1] !== ']') {
                            throw new Exception;
                        }
                        --$length;
                        ++$index;
                        if ($opiton[$index] === '=') {
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
                        if ($optionKey[$length - 1] !== ')') {
                            throw new Exception;
                        }
                        --$length;
                        $isEnumArgument = true;
                        $argumentName = '';
                    }
                    if ($char === '<') {
                        if ($optionKey[$length - 1] !== '>') {
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
        if ($isShort) {
            if (strlen($name) !== 1 || ctype_alnum($name) === false) {
                throw new Exception;
            }
        } elseif (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
            throw new Exception;
        }
        $result = array(
            'name' => $name,
            'has_argument' => -1
        );
        if ($alias !== null) {
            $result['alias'] = $alias;
        }
        if ($hasArgument) {
            if ($isEnumArgument) {
                $result['values'] = array();
                if (preg_match('/^[a-zA-Z0-9-|]+$/', $argumentName) !== 1) {
                    throw new Exception;
                }
                $result['values'] = explode('|', $argumentName);
            } elseif (preg_match('/^[a-zA-Z0-9-]+$/', $argumentName) !== 1) {
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
}
