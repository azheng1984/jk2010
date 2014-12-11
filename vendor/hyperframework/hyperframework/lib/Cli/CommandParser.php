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
                    if (isset($optionConfigs[$optionName]) === false) {
                        throw new Exception;
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
                    if ($option->isRepeatable()) {
                        if (isset($result['options'][$optionName])) {
                            $result[$optionType][$optionName][] =
                                $optionArgument;
                        } else {
                            $result[$optionType][$optionName] = [
                                $optionArgument
                            ];
                        }
                    } else {
                        $result[$optionType][$optionName] = $optionArgument;
                    }
                    $optionFullName = $option->getName();
                    if ($optionFullName !== null &&
                        isset($result[$optionType][$optionFullName]) === false
                    ) {
                        $result[$optionType][$optionFullName] =
                            $result[$optionType][$optionName];
                        $result[$optionType][$optionName] =&
                            $result[$optionType][$optionFullName];
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
                if ($option->isRepeatable()) {
                    if (isset($result['options'][$optionName])) {
                        $result[$optionType][$optionName][] = $optionArgument;
                    } else {
                        $result[$optionType][$optionName] = [$optionArgument];
                    }
                } else {
                    $result[$optionType][$optionName] = $optionArgument;
                }
                $optionShortName = $option->getShortName();
                if ($optionShortName !== null
                    && isset($result[$optionType][$optionShortName]) === false
                ) {
                    $result[$optionType][$optionShortName] =&
                        $result[$optionType][$optionName];
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
            $globalMutuallyExclusiveOptionGroupConfigs =
                $commandConfig->getMutuallyExclusiveOptionGroups();
            self::checkOptions(
                $result['global_options'],
                $globalOptionConfigs,
                $globalMutuallyExclusiveOptionGroupConfigs,
                $hasSuperOption
            );
        }
        if (isset($result['options'])) {
            $subcommand = isset($result['subcommand']) ? $result['subcommand'];
            $mutuallyExclusiveOptionGroupConfigs =
                $commandConfig->getMutuallyExclusiveOptionGroups($subcommand);
            self::checkOptions(
                $result['options'],
                $optionConfigs,
                $mutuallyExclusiveOptionGroupConfigs,
                $hasSuperOption
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
                if ($argumentConfigs[$argumentIndex]->isRepeatable()) {
                    $result['arguments'][] = [$arguments[$argumentIndex]];
                }
                $result['arguments'][] = $arguments[$argumentIndex];
            } else {
                if (isset($argumentConfigs[$argumentCount - 1]) === false) {
                    throw new CommandParsingException('Argument error.');
                }
                $argumentConfig = $argumentConfigs[$argumentCount - 1];
                if ($argumentConfig->isRepeatable()) {
                    $result['arguments'][$argumentCount - 1][] =
                        $arguments[$argumentIndex];
                } else {
                    throw new Exception;
                }
            }
        }
        $count = 0;
        foreach ($argumentConfigs as $argumentConfig) {
            if ($argumentConfig->isOptional()) {
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
                if (in_array($key, array('help', 'version'))) {
                    return true;
                }
            }
            foreach ($options as $key => $value) {
                if (in_array($key, array('help'))) {
                    return true;
                }
            }
        } else {
            foreach ($options as $key => $value) {
                if (in_array($key, array('help', 'version'))) {
                    return true;
                }
            }
        }
        return false;
    }

    private static function checkOptions(
        array $options,
        array $optionConfigs,
        array $mutuallyExclusiveOptionGroupConfigs = null,
        $hasSuperOption
    ) {
        foreach ($optionConfigs as $name => $option) {
            if ($option->getValues() !== null) {
                if (in_array($result[$name], $option->getValues()) === false) {
                    throw new Exception;
                }
            }
            if ($option->isRequired()) {
                if (isset($result[$name])) {
                    continue;
                }
                if ($hasSuperOption === false) {
                    throw new Exception;
                }
            }
        }
        if ($mutuallyExclusiveOptionGroupConfigs !== null) {
            foreach($mutuallyExclusiveOptionGroupConfigs as $groupConfig) {
                $hasOption = false;
                foreach ($groupConfig->getOptions() as $option) {
                    if (isset($options[$option->getName()])) {
                        if ($hasOption) {
                            throw new Exception;
                        }
                    }
                    $hasOption = true;
                }
                if ($groupConfig->isRequired() && $hasOption === false) {
                    if ($hasSuperOption === false) {
                        throw new Exception;
                    }
                }
            }
        }
    }
}
