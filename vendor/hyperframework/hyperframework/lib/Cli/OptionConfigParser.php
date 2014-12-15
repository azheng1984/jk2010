<?php
namespace Hyperframework\Cli;

use Exception;

class OptionConfigParser {
    private static $pattern;

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
            list($shortName, $name, $hasArgument, $argumentPattern) =
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
                    $isRepeatable = (bool)$attributes['is_repeatable'];
                }
                if (isset($attributes['is_required'])) {
                    $isRequired = (bool)$attributes['is_required'];
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
        $pattern = (string)$pattern;
        self::$pattern = $pattern;
        $length = strlen($pattern);
        if ($pattern[$length - 1] === ' ') {
            throw new Exception(self::getPatternExceptionMessage(
                'Invalid space at the end of pattern.'
            ));
        }
        if ($length < 2) {
            throw new Exception(self::getPatternExceptionMessage());
        }
        if ($pattern[0] !== '-') {
            throw new Exception(self::getPatternExceptionMessage());
        }
        $shortName = null;
        $isShort = false;
        $index = 0;
        $hasName = true;
        $hasArgumentPattern = false;
        if ($length === 2) {
            $hasName = false;
            $shortName = $pattern[1];
            $index = 2;
        } else {
            if ($pattern[1] !== '-') {
                $hasName = false;
                $shortName = $pattern[1];
                $index = 2;
                while ($length > $index && $pattern[$index] === ' ') {
                    $hasArgumentPattern = true;
                    ++$index;
                }
                if ($length > $index) {
                    $char = $pattern[$index];
                    if ($char === ',') {
                        $hasName = true;
                        $hasArgumentPattern = false;
                        ++$index;
                        while ($length > $index && $pattern[$index] === ' ') {
                            ++$index;
                        }
                    } elseif ($char === '[') {
                        if ($hasArgumentPattern === true) {
                            throw new Exception(
                                self::getPatternExceptionMessage(
                                    'Invalid space at the end of short name.'
                                )
                            );
                        }
                        $hasArgumentPattern = true;
                    } else {
                        throw new Exception(
                            self::getPatternExceptionMessage(
                                "Invalid char '$char' at the end of short name."
                            )
                        );
                    }
                } else {
                    if ($hasArgumentPattern) {
                        throw new Exception(
                            self::getPatternExceptionMessage(
                                'Invalid space at the end of short name.'
                            )
                        );
                    }
                }
            }
        }
        if ($shortName !== null && ctype_alnum($shortName) === false) {
            if ($shortName === ' ') {
                throw new Exception(self::getPatternExceptionMessage(
                    'Invalid space at the front of short name.'
                ));
            }
            throw new Exception(self::getPatternExceptionMessage(
                "Invalid short name '$shortName'."
            ));
        }
        $name = null;
        if ($hasName === true) {
            if ($length <= $index + 1 || substr($pattern, $index, 2) !== '--') {
                throw new Exception(self::getPatternExceptionMessage());
            }
            $index += 2;
            while ($index < $length) {
                $char = $pattern[$index];
                if ($char ==='[') {
                    $hasArgumentPattern = true;
                    if ($length <= $index + 1 || $pattern[$index + 1] !== '=') {
                        if (isset($pattern[$index + 1])) {
                            $char = $pattern[$index + 1];
                            if ($char === ' ') {
                                throw new Exception(
                                    self::getPatternExceptionMessage(
                                        "Invalid space after '['."
                                    )
                                );
                            } else {
                                 throw new Exception(
                                    self::getPatternExceptionMessage(
                                        "Invalid char '$char' after '['."
                                    )
                                );
                            }
                        }
                        throw new Exception(self::getPatternExceptionMessage());
                    }
                    break;
                } elseif ($char === '=') {
                    $hasArgumentPattern = true;
                    ++$index;
                    break;
                }
                $name .= $char;
                ++$index;
            }
        }
        if ($hasName === true && $name === null) {
            throw new Exception(self::getPatternExceptionMessage());
        }
        if ($name !== null) {
            if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
                if (strpos($name, ' ') !== false) {
                    if (substr($name, -1) === ' ') {
                        $name = trim($name, ' ');
                        throw new Exception(self::getPatternExceptionMessage(
                            "Invalid space at the end of name '$name'."
                        ));
                    }
                    if ($name[0] === ' ') {
                        $name = trim($name, ' ');
                        throw new Exception(self::getPatternExceptionMessage(
                            "Invalid space at the front of name '$name'."
                        ));
                    }
                }
                throw new Exception(self::getPatternExceptionMessage(
                    "Invalid space in name '$name'."
                ));
            }
        }
        $argumentPattern = null;
        $hasArgument = -1;
        if ($length > $index && $hasArgumentPattern) {
            if ($pattern[$index] === '[') {
                if ($pattern[$length - 1] !== ']') {
                    throw new Exception(self::getPatternExceptionMessage());
                }
                ++$index;
                --$length;
                if ($name !== null) {
                    ++$index;
                }
                $hasArgument = 0;
            } else {
                $hasArgument = 1;
            }
            $argumentPattern = substr($pattern, $index, $length - $index);
            if ($argumentPattern === '') {
                throw new Exception(self::getPatternExceptionMessage(
                    'Argument pattern is empty.'
                ));
            } elseif (strpos($argumentPattern, ' ') !== false) {
                throw new Exception(self::getPatternExceptionMessage(
                    'Argument pattern cannot include space.'
                ));
            } elseif ($argumentPattern[0] === '-') {
                if ($name === null) {
                    throw new Exception(self::getPatternExceptionMessage(
                        "Short option and long option must separate with ','."
                    ));
                }
                throw new Exception(self::getPatternExceptionMessage(
                    "Argument pattern cannot begin with '-'."
                ));
            }
        }
        if ($hasArgumentPattern && $argumentPattern === null) {
            throw new Exception(self::getPatternExceptionMessage());
        }
        return [$shortName, $name, $hasArgument, $argumentPattern];
    }

    private static function getPatternExceptionMessage($extraMessage = '') {
        $pattern = self::$pattren;
        $result = "Syntax of option pattern '$pattern' is invalid.";
        if ($extraMessage !== '') {
            $result .= ' ' . $extraMessage;
        }
        return $result;
    }
}
