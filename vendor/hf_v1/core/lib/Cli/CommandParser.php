<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    private $elements;
    private $collectionOptions;
    private $commandName;
    private $options;
    private $arguments;

    public static function parseCommand() {
        $count = count($_SERVER['argv']);
        $options;
        $arguments;
        $isCollection;
        $argumentIndex = 0;
        for ($index = 1; $index < $count; ++$count) {
            $element = $_SERVER['argv'][$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($isCollection) {
                    if (self::hasCommand($element) === false) {
                        throw new Exception;
                    }
                    list($commandOptions, $arguments) =
                        self::getCommandConfig($element);
                    $isCollection = false;
                    $options += $commandOptions;
                    continue;
                }
                $argCount = count($arguments);
                if ($argCount > $argumentIndex) {
                    if ($arguments[$argumentIndex]['is_collection']) {
                        $result['arguments'][] = array($element);
                    }
                    $result['argument'][] = $element;
                } else {
                    $lastArg = $arguments[$argCount - 1];
                    if ($lastArg['is_collection']) {
                        $result['arguments'][$argCount - 1][] = $element;
                    } else {
                        throw new Exception;
                    }
                }
                ++$argumentIndex;
                continue;
            }
            if ($element === '--') {
                if ($isCollection) {
                    throw new Exception;
                }
                $isArgument = true;
                continue;
            }
            if ($element[1] !== '-') {
                $i = 1;
                while ($length >= 2) {
                    $optionName = $element[$i];
                    if (isset($options[$optionName]) === false) {
                        throw new Exception;
                    }
                    $optionArg = true;
                    $option = $options[$optionName];
                    if ($option['has_argument'] === 0) {
                        if ($length > 2) {
                            $optionArg = substr($element, 1 + $i);
                        }
                    } elseif ($option['has_argument'] === 1) {
                        if ($length > 2) {
                            $optionArg = substr($element, 1 + $i);
                        } else {
                            ++$index;
                            if ($index >= $count) {
                                throw new Exception;
                            }
                            $optionArg = $_SERVER['argv'][$index];
                        }
                    }
                    foreach ($option['mutex_options'] as $item) {
                        if (isset($result['options'][$item])) {
                            throw new Exception;
                        }
                    }
                    if ($option['is_repeatable']) {
                        if (isset($result['options'][$optionName])) {
                            $result['options'][$optionName][] = $optionArg;
                        } else {
                            $result['options'][$optionName] = array($optionArg);
                        }
                    }
                    $result['options'][$optionName] = $optionArg;
                    if ($optionArg !== true) {
                        break;
                    }
                    ++$i;
                    --$length;
                }
            } else {
                $optionArg = true;
                $optionName = substr($element[0], 2);
                if (strpos($element, '=') !== false) {
                    list($optionName, $optionArg) = explode('=', $element, 2);
                }
                if (isset($options[$optionName])) {
                    throw new Exception;
                }
                $option = $options[$optionName];
                if ($option['has_argument'] === 0) {
                    $result['options'][] = array($optionName, $optionArg);
                } elseif ($option['has_argument'] === 1) {
                    if ($optionArg === null) {
                        ++$index;
                        if ($index >= $count) {
                            throw new Exception;
                        }
                        $optionArg = $_SERVER['argv'][$index];
                    }
                    $result['options'][$optionName] = $optionArg;
                } else {
                    if ($optionArg !== null) {
                        throw new Exception;
                    }
                }
            }
        }
        foreach ($requiredOptions as $index => $item) {
            if (is_array($item)) {
                foreach ($item as $option) {
                    if (isset($result['options'][$option]) === false) {
                        unset($requiredOptions[$index]);
                        continue 2;
                    }
                }
            } elseif (isset($result['options'][$item]) === false) {
                unset($requiredOptions[$index]);
                continue;
            }
            throw new Exception;
        }
        if ($argIndex < self::getRequiredArgumentNumber()) {
            throw new Exception;
        }
    }

    //用于继承 +  construct 自动处理部分选项
    public static function getOptions() {
    }

    //过滤器自动验证, give argument a name
    public static function getArguments() {
    }

    //通过过滤器处理时用于区分
    public static function getCommandName() {
    }
}
