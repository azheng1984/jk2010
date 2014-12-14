<?php
namespace Hyperframework\Cli;

use Exception;

class OptionConfigParser {
    public static function parse($configs) {
        if (is_array($configs) === false) {
            throw new Exception;
        }
        $result = [];
        foreach ($configs as $pattern => $attributes) {
            if (is_int($pattern)) {
                $pattern = $attributes;
                $attributes = null;
            }
            list($name, $shortName, $hasArgument, $argumentPattern) =
                static::parsePattern($pattern);
            $description = null;
            $isRequired = false;
            $isRepeatable = false;
            if (is_string($attributes)) {
                $description = $attributes;
            } elseif (is_array($attributes)) {
                if (isset($attributes['description'])) {
                    $description = $description;
                }
                if (isset($attributes['is_repeatable'])) {
                    $isRepeatable = $isRepeatable;
                }
                if (isset($attributes['is_required'])) {
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
                $argumentPattern
            );
            if ($name !== null) {
                if (isset($result[$name])) {
                    throw new Exception;
                }
                $result[$name] = $option;
            }
            if ($shortName !== null) {
                if (isset($result[$shortName])) {
                    throw new Exception;
                }
                $result[$shortName] = $option;
            }
        }
        return $result;
    }

    private static function parsePattern($pattern) {
        $length = strlen($pattern);
        if ($length < 2) {
            throw new Exception;
        }
        if ($pattern[0] !== '-') {
            throw new Exception;
        }
        $shortName = null;
        $isShort = false;
        $pattern = str_replace(' ', '', $pattern);
        $index = 0;
        if ($length === 2) {
            $shortName = $pattern[1];
            $index = 2;
        } else {
            if ($length > 3 && $pattern[2] === ',') {
                $shortName = $pattern[2];
                $index = 3;
            }
        }
        if (ctype_alnum($shortName) === false) {
            throw new Exception;
        }
        $name = null;
        if ($length > $index && $pattern[$index] === '-') {
            if (isset($pattern[$index + 1]) || $pattern[$index + 1] !== '-') {
                throw new Exception;
            }
            $index += 2;
            while ($index < $length) {
                $char = $pattern[$index];
                if ($char ==='[') {
                    if (isset($pattern[$index + 1])
                        && $pattern[$index + 1] === '='
                    ) {
                        $pattern[$index + 1] = '[';
                        ++$index;
                        break;
                    }
                } elseif ($char === '=') {
                    ++$index;
                    break;
                }
                $name .= $char;
                ++$index;
            }
        }
        if ($name !== null) {
            if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
                throw new Exception;
            }
        }
        $argumentPattern = null;
        $hasArgument = -1;
        if (isset($pattern[$index])) {
            if ($pattern[$index] === '[') {
                if ($pattern[$length - 1] !== ']')  {
                    throw new Exception;
                }
                ++$index;
                --$length;
                $hasArgument = 0;
            } else {
                $hasArgument = 1;
            }
            $argumentPattern = substr($pattern, $index, $length - $index);
        }
        return [$name, $shortName, $hasArgument, $argumentPattern];
    }
}
