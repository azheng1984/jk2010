<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    public static function _test() {}

    public static function parse($hasMultipleCommands) {
        $arguments = null;
        $options = null;
        $opitons = static::getOptionConfig();
        if ($hasMultipleCommands === false) {
            $arguments = static::getArgumentConfig();
        }
        $optionType = 'global_options';
        $isGlobal = $hasMultipleCommands;
        $count = count($_SERVER['argv']);
        $isArgument = false;
        $argumentIndex = 0;
        $result = array('arguments' => [], 'options' => []);
        if ($hasMultipleCommands) {
            $result['global_options'] = [];
        }
        for ($index = 1; $index < $count; ++$index) {
            $element = $_SERVER['argv'][$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($isGlobal) {
                    $result['subcommand'] = $element;
                    $arguments = static::getArgumentConfig($element);
                    $options = static::getOptionConfig($element) + $options;
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
                            $optionArgument = $_SERVER['argv'][$index];
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
                            $result[$optionType][$optionName][] = $optionArgument;
                        } else {
                            $result[$optionType][$optionName] = array($optionArgument);
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
                    list($optionName, $optionArgument) = explode('=', $element, 2);
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
                        $optionArgument = $_SERVER['argv'][$index];
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
                        $result[$optionType][$optionName] = array($optionArgument);
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

    private static function checkOptions($config, $result) {
        foreach ($config as $name => $option) {
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

    protected static function getOptionConfig($subcommand = null) {
        return CommandConfig::get('options', $subcommand);
    }

    protected static function getArgumentConfig($subcommand = null) {
        return CommandConfig::get('arguments', $subcommand);
    }
}
