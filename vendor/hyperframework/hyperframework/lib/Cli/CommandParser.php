<?php
namespace Hyperframework\Cli;

use Exception;

class CommandParser {
    public static function parse($commandConfig, array $argv = null) {
        if ($argv === null) {
            $argv = $_SERVER['argv'];
        }
        $optionConfigs = $commandConfig->getOptions();
        $result = [];
        $optionType = null;
        if ($commandConfig->isSubcommandEnabled()) {
            $result['global_options'] = [];
            $optionType = 'global_options';
        } else {
            $result['options'] = [];
            $result['arguments'] = [];
            $optionType = 'options';
        }
        $isGlobal = $commandConfig->isSubcommandEnabled();
        $count = count($argv);
        $isArgument = false;
        $arguments = [];
        for ($index = 1; $index < $count; ++$index) {
            $element = $argv[$index];
            $length = strlen($element);
            if ($length === 0
                || $element[0] !== '-'
                || $element === '-'
                || $isArgument
            ) {
                if ($isGlobal) {
                    if ($config->hasSubcommand($element) === false) {
                        throw new Exception;
                    }
                    $isGlobal= false;
                    $result['subcommand'] = $element;
                    $result['option'] = [];
                    $result['arguments'] = [];
                    $optionConfigs = $commandConfig->getOptions($element);
                    $optionType = 'options';
                } else {
                    $arguments[] = $element;
                }
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
                    var_dump($optionConfigs);
                    if (isset($optionConfigs[$optionName]) === false) {
                        throw new Exception;
                    }
                    if (isset($optionConfigs[$optionName]['full_name'])) {
                        $optionName = $optionConfigs[$optionName]['full_name'];
                    }
                    $optionArgument = true;
                    $option = $optionConfigs[$optionName];
                    if ($option->hasArgument() === 0) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        }
                    } elseif ($option->hasArgument() === 1) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        } else {
                            ++$index;
                            if ($index >= $count) {
                                throw new CommandParsingException;
                            }
                            $optionArgument = $argv[$index];
                        }
                    }
                    if (isset($option['mutex_options'])) {
                        foreach ($option['mutex_options'] as $item) {
                            if (isset($result['options'][$item])) {
                                throw new CommandParsingException;
                            }
                        }
                    }
                    if (isset($option['is_repeatable'])
                        && $option['is_repeatable']
                    ) {
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
                if (isset($optionConfigs[$optionName]) === false) {
                    throw new Exception;
                }
                $option = $optionConfigs[$optionName];
                if ($option->hasArgument() === 1) {
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
                if ($option->isRepeatable()) {
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
        $hasSuperOption = static::hasSuperOption(
            isset($result['global_options']) ? $result['global_options'] : [],
            isset($result['subcommand']) ? $result['subcommand'] : null,
            isset($result['options']) ? $result['options'] : [],
            $commandConfig
        );
        if (isset($result['global_options'])) {
            $globalOptionConfigs = $commandConfig->getOptions();
            self::checkOptions(
                $globalOptionConfigs, $result['global_options'], $hasSuperOption
            );
        }
        if (isset($result['options'])) {
            self::checkOptions(
                $optionConfigs, $result['options'], $hasSuperOption
            );
        }
        if ($hasSuperOption || $isGlobal) {
            return $result;
        }
        $argumentConfigs = null;
        if ($commandConfig->isSubcommandEnabled()) {
            $argumentConfigs = $commandConfig->getArguments(
                $result['subcommand']
            );
        } else {
            $argumentConfigs = $commandConfig->getArguments();
        }
        $argumentConfigCount = count($argumentConfigs);
        $argumentCount = count($arguments);
        for ($argumentIndex = 0;
            $argumentIndex < $argumentCount;
            ++$argumentIndex
        ) {
            if ($argumentConfigCount > $argumentIndex) {
                if ($argumentConfigs[$argumentIndex]['is_repeatable']) {
                    $result['arguments'][] = array($element);
                }
                $result['arguments'][] = $element;
            } else {
                if (isset($argumentConfigs[$argumentCount - 1]) === false) {
                    throw new CommandParsingException('Argument error.');
                }
                $lastArgument = $argumentConfigs[$argumentCount - 1];
                if ($lastArgument['is_array']) {
                    $result['arguments'][$argumentCount - 1][] = $element;
                } else {
                    throw new Exception;
                }
            }
        }
        $count = 0;
        foreach ($argumentConfigs as $argumentConfig) {
            if (isset($argumentConfig['is_optional'])
                && $argumentConfig['is_optional']
            ) {
                break;
            }
            ++$count;
            if ($count > $argumentCount) {
                throw new Exception;
            }
        }
        return $result;
    }

    protected static function hasSuperOption(
        array $globalOptions, $subcommand, array $options, $commandConfig
    ) {
        if ($commandConfig->isSubcommandEnabled()) {
            foreach ($globalOptions as $key => $value) {
                if (in_array($key, array('h', 'help', 'version'))) {
                    return true;
                }
            }
            foreach ($options as $key => $value) {
                if (in_array($key, array('h', 'help'))) {
                    return true;
                }
            }
        } else {
            foreach ($options as $key => $value) {
                if (in_array($key, array('h', 'help', 'version'))) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function checkOptions($configs, $options, $hasSuperOption) {
        foreach ($configs as $name => $option) {
            if ($option->getValues() !== null) {
                if (in_array($result[$name], $option->getValues()) === false) {
                    throw new Exception;
                }
            }
            if ($option->isRequired()) {
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
                if ($hasSuperOption === false) {
                    throw new Exception;
                }
            }
        }
    }
}
