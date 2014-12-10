<?php
namespace Hyperframework\Cli;

use Exception;

class OptionConfigBuilder {
    public static function build($configs) {
        if (is_array($config) === false) {
            if (is_string($config) === false) {
                throw new Exception;
            }
            $config = array($config);
        }
        return static::parseGroup($config);
    }

    //delete option group
    protected static function parseGroup(
        array $config, array $attributes = array()
    ) {
        $result = array();
        $shouldCheckAttribute = true;
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    $result = self::parseGroup($value, $attributes) + $result;
                    $shouldCheckAttribute = false;
                    continue;
                } else {
                    if ($shouldCheckAttribute) {
                        if (isset($value[0]) && $value[0] !== '-') {
                            $attributes[$value] = true;
                            continue;
                        }
                    }
                    $key = $value;
                    $value = array();
                }
            } elseif ($shouldCheckAttribute
                && isset($key[0])
                && $key[0] !== '-'
            ) {
                $attributes[$key] = $value;
                continue;
            }
            $shouldCheckAttribute = false;
            $option = static::parseKey($key);
            $name = $option['name'];
            unset($option['name']);
            $result[$name] = $option + $value + $attributes;
            if (isset($option['short_name'])) {
                $result[$option['short_name']] = array('full_name' => $name);
            }
        }
        return $result;
    }

    protected static function parseKey($optionKey) {
        $length = strlen($optionKey);
        if ($length < 2) {
            throw new Exception;
        }
        if ($optionKey[0] !== '-') {
            throw new Exception;
        }
        $shortName = null;
        $isShort = false;
        if (strpos($optionKey, ',') !== false) {
            $tmps = explode(',', $optionKey, 2);
            $shortOption = $tmps[0];
            if (strlen($shortOption) !== 2) {
                throw new Exception;
            }
            $shortName = $shortOption[1];
            if (ctype_alnum($shortName) === false) {
                throw new Exception;
            }
            $optionKey = ltrim($tmps[1]);
            $length = strlen($optionKey);
        } elseif ($optionKey[1] !== '-') {
            $isShort = true;
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
        if ($shortName !== null) {
            $result['short_name'] = $shortName;
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
