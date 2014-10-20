<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    private static $commandName = null;
    private static $collectionOptions = array();
    private static $options = array();
    private static $arguments = array();

    private static function getCollectionOptions() {
    }

    private static function getCommandConfig($name = null) {
    }

    public static function execute($isCommandCollection) {
        $arguments = array();
        $options = array();
        if ($isCommandCollection) {
            $opitons = self::getCollectionOptions();
        } else {
            list($options, $arguments) = self::getCommandConfig($element);
        }
        $hasCommand = $isCommandCollection;
        $count = count($_SERVER['argv']);
        $isArgument = false;
        $argumentIndex = 0;
        $result = array('arguments' => array(), 'options' => array());
        for ($index = 1; $index < $count; ++$count) {
            $element = $_SERVER['argv'][$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($hasCommand) {
                    list($commandOptions, $arguments) =
                        self::getCommandConfig($element);
                    $hasCommand = false;
                    $options += $commandOptions;
                    continue;
                }
                $argumentCount = count($arguments);
                if ($argumentCount > $argumentIndex) {
                    if ($arguments[$argumentIndex]['is_collection']) {
                        $result['arguments'][] = array($element);
                    }
                    $result['arguments'][] = $element;
                } else {
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
                if ($hasCommand) {
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
                    if (isset($options[$optionName]['name'])) {
                        $optionName = $options[$optionName]['name'];
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
                            $result['options'][$optionName][] = $optionArgument;
                        } else {
                            $result['options'][$optionName] = array($optionArgument);
                        }
                    } else {
                        $result['options'][$optionName] = $optionArgument;
                    }
                    if ($optionArgument !== true) {
                        break;
                    }
                    ++$charIndex;
                    --$length;
                }
            } else {
                $optionArgument = true;
                $optionName = substr($element[0], 2);
                if (strpos($element, '=') !== false) {
                    list($optionName, $optionArgument) = explode('=', $element, 2);
                }
                if (isset($options[$optionName])) {
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
                if ($option['is_repeatable']) {
                    if (isset($result['options'][$optionName])) {
                        $result['options'][$optionName][] = $optionArgument;
                    } else {
                        $result['options'][$optionName] = array($optionArgument);
                    }
                } else {
                    $result['options'][$optionName] = $optionArgument;
                }
            }
        }
        foreach ($options as $name => $option) {
            if (isset($option['values'])) {
                if (in_array($result['options'][$name], $option['values']) === false) {
                    throw new Exception;
                }
            }
            if (isset($option['is_required']) && $option['is_required']) {
                if (isset($result['options'][$name])) {
                    continue;
                }
                if (isset($option['mutex_options'])) {
                    foreach ($option['mutex_options'] as $mutexOption) {
                        if (isset($result['options'][$mutexOption])) {
                            continue 2;
                        }
                    }
                }
                throw new Exception;
            }
        }
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
        if ($isCommandCollection) {
            $result['collection_options'] = array();
            foreach ($result['options'] as $name => $value) {
                if ($options[$name]['is_collection_option']) {
                    $result['collection_options'][$name] = $value;
                    unset($result['options'][$name]);
                }
            }
        }
        return $result;
    }

    //用于继承 +  construct 自动处理部分选项
    public static function getCollectionOptions() {
    }

    public static function getOptions() {
    }

    //过滤器自动验证, give argument a name
    public static function getArguments() {
    }

    //通过过滤器处理时用于区分
    public static function getCommandName() {
    }
}
