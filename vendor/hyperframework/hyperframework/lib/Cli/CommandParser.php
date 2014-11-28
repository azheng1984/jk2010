<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    //add special option --version and --help(must no other thing!, otherwise checking!)
    //if throw parsing error, also add parsed options
    //can reparse use new command config (help as subcommand)
    //command_name help (other options)
    public static function parse($commandConfig, array $argv = null) {
        $arguments = null;
        $options = $commandConfig->get('options');
        $result = array('arguments' => [], 'options' => []);
        $optionType = null;
        if ($commandConfig->hasMultipleCommands()) {
            $result['global_options'] = [];
            $optionType = 'global_options';
        } else {
            $arguments = $commandConfig->get('arguments');
            $optionType = 'options';
        }
        $isGlobal = $commandConfig->hasMultipleCommands();
        if ($argv === null) {
            $argv = $_SERVER['argv'];
        }
        $count = count($argv);
        $isArgument = false;
        $argumentIndex = 0;
        for ($index = 1; $index < $count; ++$index) {
            $element = $argv[$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($isGlobal) {
                    //todo ????!!!!
                    if (static::hasSubcommand($element) === false) {
                        throw new Exception;
                    }
                    static::checkOption($options, $result[$optionType]);
                    $result['subcommand'] = $element;
                    $options = $commandConfig->get('options', $element);
                    $arguments = $commandConfig->get('arguments', $element);
                    $isGlobal= false;
                    $optionType = 'options';
                    continue;
                }
                $argumentCount = count($arguments);
                if ($argumentCount > $argumentIndex) {
                    if ($arguments[$argumentIndex]['is_collection']) {
                        $result['arguments'][] = array($element);
                    }
                    $result['arguments'][] = $element;
                } else {
                    if (isset($arguments[$argumentCount - 1]) === false) {
                        throw new Exception;
                    }
                    $lastArgument = $arguments[$argumentCount - 1];
                    if ($lastArgument['is_collection']) {
                        $result['arguments'][$argumentCount - 1][] = $element;
                    } else {
                        throw new Exception;
                    }
                }
                ++$argumentIndex;
                continue;
            }
            if ($element === '--') {
                if ($isGlobal) {
                    throw new Exception;
                }
                $isArgument = true;
                continue;
            }
            if ($element[1] !== '-') {
                $charIndex = 1;
                while ($length > 1) {
                    $optionName = $element[$charIndex];
                    if (isset($options[$optionName]) === false) {
                        throw new Exception;
                    }
                    if (isset($options[$optionName]['full_name'])) {
                        $optionName = $options[$optionName]['full_name'];
                    }
                    $optionArgument = true;
                    $option = $options[$optionName];
                    if ($option['has_argument'] === 0) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        }
                    } elseif ($option['has_argument'] === 1) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        } else {
                            ++$index;
                            if ($index >= $count) {
                                throw new Exception;
                            }
                            $optionArgument = $argv[$index];
                        }
                    }
                    if (isset($option['mutex_options'])) {
                        foreach ($option['mutex_options'] as $item) {
                            if (isset($result['options'][$item])) {
                                throw new Exception;
                            }
                        }
                    }
                    if ($option['is_repeatable']) {
                        if (isset($result['options'][$optionName])) {
                            $result[$optionType][$optionName][] =
                                $optionArgument;
                        } else {
                            $result[$optionType][$optionName] =
                                array($optionArgument);
                        }
                    } else {
                        $result[$optionType][$optionName] = $optionArgument;
                    }
                    if ($optionArgument !== true) {
                        break;
                    }
                    ++$charIndex;
                    --$length;
                }
            } else {
                $optionArgument = true;
                $optionName = substr($element, 2);
                if (strpos($element, '=') !== false) {
                    list($optionName, $optionArgument) =
                        explode('=', $element, 2);
                }
                if (isset($options[$optionName]) === false) {
                    throw new Exception;
                }
                $option = $options[$optionName];
                if ($option['has_argument'] === 1) {
                    if ($optionArgument === null) {
                        ++$index;
                        if ($index >= $count) {
                            throw new Exception;
                        }
                        $optionArgument = $argv[$index];
                    }
                } else {
                    if ($optionArgument !== true) {
                        throw new Exception;
                    }
                }
                if (isset($option['mutex_options'])) {
                    foreach ($option['mutex_options'] as $item) {
                        if (isset($result['options'][$item])) {
                            throw new Exception;
                        }
                    }
                }
                if (isset($option['is_repeatable'])
                    && $option['is_repeatable']
                ) {
                    if (isset($result['options'][$optionName])) {
                        $result[$optionType][$optionName][] = $optionArgument;
                    } else {
                        $result[$optionType][$optionName] =
                            array($optionArgument);
                    }
                } else {
                    $result[$optionType][$optionName] = $optionArgument;
                }
            }
        }
        self::checkOptions($options, $result['options']);
        $count = 0;
        foreach ($arguments as $argument) {
            if ($argument['is_optional']) {
                break;
            }
            ++$count;
            if ($count > $argumentIndex) {
                throw new Exception;
            }
        }
        return $result;
    }

    private static function checkOptions($configs, $result) {
        foreach ($configs as $name => $option) {
            if (isset($option['values'])) {
                if (in_array($result[$name], $option['values']) === false) {
                    throw new Exception;
                }
            }
            if (isset($option['is_required']) && $option['is_required']) {
                if (isset($result[$name])) {
                    continue;
                }
                if (isset($option['mutex_options'])) {
                    foreach ($option['mutex_options'] as $mutexOption) {
                        if (isset($result[$mutexOption])) {
                            continue 2;
                        }
                    }
                }
                throw new Exception;
            }
        }
    }
}
