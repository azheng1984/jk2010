<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class OptionConfigParser {
    private static $pattern;

    public static function parse(array $configs) {
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
                    throw new ConfigException(
                        "Option config error. Option '--$name' 不允许重复."
                    );
                }
                $result[$name] = $option;
            }
            if ($shortName !== null) {
                if (isset($result[$shortName])) {
                    throw new ConfigException(
                        "Option config error. Option '-$name' 不允许重复."
                    );
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
            throw self::createPatternException(
                'Invalid space at the end of pattern.'
            );
        }
        if ($length < 2) {
            throw self::createPatternException();
        }
        if ($pattern[0] !== '-') {
            throw self::createPatternException();
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
                throw self::createPatternException(
                    'Invalid space at the front of short name.'
                );
            }
            throw self::createPatternException(
                "Invalid short name '$shortName'."
            );
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
                throw self::createPatternException();
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
                                $char = 'space';
                            } else {
                                $char = "char '$char'";
                            }
                            throw self::createPatternException(
                               "Invalid $char after '[', '=' is expected."
                            );

                        }
                        throw self::createPatternException();
                    }
                    if ($pattern[$length - 1] !== ']') {
                        throw self::createPatternException(
                            '方括号没有闭合.'
                        );
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
            throw self::createPatternException();
        }
        if ($name !== null) {
            if (preg_match('/^[a-zA-Z0-9-]{2,}$/', $name) !== 1) {
                $spacePosition = strpos($name, ' ');
                if ($spacePosition !== false) {
                    if (substr($name, -1) === ' ') {
                        $name = trim($name, ' ');
                        throw self::createPatternException(
                            "Invalid space at the end of name '$name'."
                        );
                    }
                    if ($name[0] === ' ') {
                        $name = trim($name, ' ');
                        throw self::createPatternException(
                            "Invalid space at the front of name '$name'."
                        );
                    }
                }
                if (preg_match('/^[a-zA-Z0-9-]+/', $name, $matches) === 1) {
                    $char = $name[strlen($matches[0]) + 1];
                    if ($char === ' ') {
                        $char = 'space';
                    } else {
                        $char = "'$char'";
                    }
                    $name = $matches[0];
                    if (strlen($name) === 1) {
                        throw self::createPatternException(
                            "Invalid long option name '$name'."
                        );
                    }
                    throw self::createPatternException(
                        "Invalid $char after name '$name', '=' is expected."
                    );
                }
                throw self::createPatternException();
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
                    throw self::createPatternException(
                        'Argument pattern cannot be empty.'
                    );//-x[]
                } else {
                    throw self::createPatternException(
                        'Invalid space at the end of short name.'
                    );
                }
            } else {
                throw self::createPatternException(
                    'Argument pattern cannot be empty.'
                );//--xx[=] or --x=
            }
        } elseif (strpos($argumentPattern, ' ') !== false) {
            throw self::createPatternException(
                'Argument pattern cannot include space.'
            );
        } elseif ($argumentPattern[0] === '-') {
            if ($name === null) {
                throw self::createPatternException(
                    "Short option and long option must separate with ','."
                );
            }
            throw self::createPatternException(
                "Argument pattern cannot begin with '-'."
            );
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
                    throw self::createPatternException(
                        "'[' or ']' is not closed"
                    ); // -x[[x]
                }
                if ($roundBracketDepth !== 0) {
                    throw self::createPatternException(
                        "'(' or ')' is not closed"
                    ); // -x([x]
                }
                if ($isOptional === false) {
                    if ($isShortOption) {// -x [<arg>]
                        throw self::createPatternException(
                            'Invalid space at the end of short name.'
                        );
                    } else {//--xx=[<arg>]
                        throw self::createPatternException(
                            'option argument is optional.'
                        );
                    }
                }
            } elseif ($isOptional) {// -x[arg]<arg> or -x<arg>
                $isOptional = false;
            }
        }
        return $argumentPattern;
    }

    private static function createPatternException($extraMessage = '') {
        $pattern = self::$pattern;
        $result = "Option config error. Pattern '$pattern' is invalid.";
        if ($extraMessage !== '') {
            $result .= ' ' . $extraMessage;
        }
        return new ConfigException($result);
    }
}
