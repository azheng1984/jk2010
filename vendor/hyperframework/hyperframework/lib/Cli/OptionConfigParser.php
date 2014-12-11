<?php
namespace Hyperframework\Cli;

use Exception;

class OptionConfigParser {
    public static function parse($configs) {
        if (is_array($configs) === false) {
            throw new Exception;
        }
        $result = [];
        foreach ($configs as $key => $value) {
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }
            list($name, $shortName, $hasArgument, $argumentName, $values) =
                static::parseKey($key);
            $description = null;
            $isRequired = false;
            $isRepeatable = false;
            if (is_string($value)) {
                $description = $value;
            } elseif (is_array($value)) {
                if (isset($value['description'])) {
                    $description = $description;
                }
                if (isset($value['is_repeatable'])) {
                    $isRepeatable = $isRepeatable;
                }
                if (isset($value['is_required'])) {
                    $isRequired = $isRequired;
                }
            }
            $option = new OptionConfig(
                $name,
                $shortName,
                $description,
                $isRepeatable,
                $isRequired,
                $hasArgument,
                $argumentName,
                $values
            );
            if ($name !== null) {
                $result[$name] = $option;
            }
            if ($shortName !== null) {
                $result[$shortName] = $option;
            }
        }
        return $result;
    }

    private static function parseKey($key) {
        $length = strlen($key);
        if ($length < 2) {
            throw new Exception;
        }
        if ($key[0] !== '-') {
            throw new Exception;
        }
        $shortName = null;
        $isShort = false;
        if (strpos($key, ',') !== false) {
            $tmps = explode(',', $key, 2);
            $shortOption = $tmps[0];
            if (strlen($shortOption) !== 2) {
                throw new Exception;
            }
            $shortName = $shortOption[1];
            if (ctype_alnum($shortName) === false) {
                throw new Exception;
            }
            $key = ltrim($tmps[1]);
            $length = strlen($key);
        } elseif ($key[1] !== '-') {
            $isShort = true;
            $key = '-' . $key;
            ++$length;
            if ($length > 2) {
                if ($key[2] === '[') {
                    $key = str_replace('[', '[=', 1);
                    ++$length;
                } elseif ($key[2] === '<') {
                    $key = str_replace('<', ' <', 1);
                    ++$length;
                } elseif ($key[2] === '(') {
                    $key = str_replace('(', ' (', 1);
                    ++$length;
                }
            }
        }
        $name = null;
        $argumentName = null;
        $isOptionalArgument = false;
        $hasArgument = false;
        $hasValues = false;
        for ($index = 2; $index < $length; ++$index) {
            $char = $key[$index];
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
                        if ($key[$length - 1] !== ']') {
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
                        if ($key[$length - 1] !== ')') {
                            throw new Exception;
                        }
                        --$length;
                        $hasValues = true;
                        $argumentName = '';
                    }
                    if ($char === '<') {
                        if ($key[$length - 1] !== '>') {
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
        $hasArgument = -1;
        $values = null;
        if ($hasArgument) {
            if ($hasValues) {
                $values = array();
                if (preg_match('/^[a-zA-Z0-9-|]+$/', $argumentName) !== 1) {
                    throw new Exception;
                }
                $values = explode('|', $argumentName);
                $argumentName = null;
            } elseif (preg_match('/^[a-zA-Z0-9-]+$/', $argumentName) !== 1) {
                throw new Exception;
            }
            if ($isOptoinalArgument) {
                $hasArgument = 0;
            } else {
                $hasArgument = 1;
            }
        }
        return [$name, $shortName, $hasArgument, $argumentName, $values];
    }
}
