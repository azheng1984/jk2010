<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    public static function parseCommand() {
    }
//    -d ((-c <list>)|(-a <max>))
//    'usage' => array(
//        'usage_name' => '--main --opt[=<arg>] (--opt1 | --opt2 | --opt3) <arg>',
//        '--main --opt[=<arg>]
//        [--a --b (--c|--d|(--g [-d]))] (--opt1|--opt2|--opt3) [--x|--y|--x]<arg>',
//        '([-h|--header] --opt[=(on|off)]|(--opt1|--opt2|--opt3[=(on|off)])) <arg>',
//        '[options] [<arg>]...',
//        '[options] command'
//    ),
// --c (--a|--b|--c) [--a] [--b] [<file>...]
//   (--a|--b [--c])
//    array(
//        'options' => array(
//        ),
//        'arguments' => array(
//        'arg1', '[<arg2>]', '[<arg2>]...'
//    );
//    'Arguments:' => array(
//        'xx' => 'xx',
//        'xx2' => 'xx',
//    );

    public static function parseUsage($usage) {
        $length = strlen($usage);
        $items = explode(' ', $usage);
        for ($index = 0; $index < $length; ++$length) {
            $char = $usage[$index];
            if ($char === '-') {
                //option
            }
            if ($char === '(') {
                //required
            }
            if ($char === '[') {
                //optional
            }
            if ($char === '<') {
                //argument
            }
            //command-line
        }
    }

    private static function parseUsageOption($usage, &$index) {
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
                if (strpos($key, ', ') !== false) {
                    $items = explode(', ', $key);
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
