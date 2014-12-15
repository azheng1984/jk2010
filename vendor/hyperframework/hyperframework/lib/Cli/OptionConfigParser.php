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
                    $description = $attributes['description'];
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
        $argumentPattern = null;
        $hasArgument = -1;
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
                    $hasArgument = 1;
                    ++$index;
                }
                if ($length > $index) {
                    $char = $pattern[$index];
                    if ($char === ',') {
                        $hasName = true;
                        $hasArgument = -1;
                        ++$index;
                        while ($length > $index && $pattern[$index] === ' ') {
                            ++$index;
                        }
                    } else {
                        //-x<arg> -x(enum1|enum2) -xenum1|enum2
                        if ($hasArgument === -1) {
                            $hasArgument = 0;
                        }
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
        if ($hasArgument !== -1) {
            $isOptional = $hasArgument !== 1;
            $argumentPattern = self::getArgumentPattern(
                true, $index, $length, $isOptional
            );
            $hasArgument = $isOptional === true ? 0 : 1;
        }
        $name = null;
        if ($hasName === true) {
            if ($length <= $index + 1 || substr($pattern, $index, 2) !== '--') {
                throw new Exception(self::getPatternExceptionMessage());
            }
            $name = '';
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
                                        "Invalid space after '[', '=' is expected."
                                    )
                                );
                            } else {
                                 throw new Exception(
                                    self::getPatternExceptionMessage(
                                        "Invalid char '$char' after '[', '=' is expected."
                                    )
                                );
                            }
                        }
                        throw new Exception(self::getPatternExceptionMessage());
                    }
                    if ($pattern[$length - 1] !== ']') {
                        throw new Exception;
                    }
                    $argumentPattern = self::getArgumentPattern(
                        false, $index + 2, $length - 1
                    );
                    $hasArgument = 0;
                    break;
                } elseif ($char === '=') {
                    $isOptional = false;
                    $argumentPattern = self::getArgumentPattern(
                        false, $index + 1, $length, $isOptional 
                    );
                    $hasArgument = 1;
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
                    throw new Exception(self::getPatternExceptionMessage(
                        "Invalid space in name '$name'."
                    ));
                }
                throw new Exception(self::getPatternExceptionMessage(
                    "Invalid long option name '$name'."
                ));
            }
        }
        return [$shortName, $name, $hasArgument, $argumentPattern];
    }

    private static function getArgumentPattern(
        $isShortOption, $index, $length, &$isOptional = null
    ) {
        $pattern = self::$pattern;
        $argumentPattern = substr($pattern, $index, $length - $index);
        if ($argumentPattern === '') {
            if ($isShortOption) {
                if ($isOptional) {
                    throw new Exception; //-x[
                } else {
                    throw new Exception(
                        self::getPatternExceptionMessage(
                            'Invalid space at the end of short name.'
                        )
                    );
                }
            } else {
                throw new Exception; //--xx[=] or --x=
            }
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
        if ($isOptional !== null) {
            $roundBracketDepth = 0;
            $squareBracketDepth = 0;
            while ($length > $index) {
                $char = $pattern[$index];
                if ($char === '[') {
                    ++$squareBracketDepth;
                } elseif ($char === ']') {
                    --$squareBracketDepth;
                } else {
                    if ($squareBracketDepth <= 0) {
                        if ($char === '(') {
                            ++$roundBracketDepth;
                        } elseif ($char === ')') {
                            --$roundBracketDepth;
                        } else {
                            break;
                        }
                    }
                }
                ++$index;
            }
            if ($length === $index) {
                if ($squareBracketDepth !== 0) {
                    throw new Exception(self::getPatternExceptionMessage(
                        "'[' or ']' is not closed"
                    )); // -x[[x]
                }
                if ($roundBracketDepth !== 0) {
                    throw new Exception(self::getPatternExceptionMessage(
                        "'(' or ')' is not closed"
                    )); // -x([x]
                }
                if ($isOptional === false) {
                    if ($isShortOption) {// -x [<arg>]
                        throw new Exception(
                            self::getPatternExceptionMessage(
                                'Invalid space at the end of short name.'
                            )
                        );
                    } else {//--xx=[<arg>]
                        throw new Exception(self::getPatternExceptionMessage(
                            'option argument is optional.'
                        ));
                    }
                }
            } else {
                if ($isOptional) {
                    if ($isShortOption) {// -x[arg]<arg> or -x<arg>
                        $isOptional = false;
                    } else {//never
                        throw new Exception;
                    }
                }
            }
        }
        return $argumentPattern;
    }

    private static function getPatternExceptionMessage($extraMessage = '') {
        $pattern = self::$pattern;
        $result = "Syntax of option pattern '$pattern' is invalid.";
        if ($extraMessage !== '') {
            $result .= ' ' . $extraMessage;
        }
        return $result;
    }
}
