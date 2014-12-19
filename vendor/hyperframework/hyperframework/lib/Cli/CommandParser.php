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
        $subcommand = null;
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
                        throw new CommandParsingException(
                            "Command $element not found"
                        );
                    }
                    $isGlobal = false;
                    $subcommand = $element;
                    $result['subcommand'] = $element;
                    $result['option'] = [];
                    $optionConfigs = $commandConfig->getOptions($element);
                    $optionType = 'options';
                } else {
                    $arguments[] = $element;
                }
                continue;
            }
            if ($element === '--') {
                if ($isGlobal) {
                    throw new CommandParsingException(
                        "Option -- is not allowed"
                    );
                }
                $isArgument = true;
                continue;
            }
            if ($element[1] !== '-') {
                $charIndex = 1;
                while ($length > 1) {
                    $optionName = $element[$charIndex];
                    if (isset($optionConfigs[$optionName]) === false) {
                        $message = "Option $optionName is not allowed";
                        if ($subcommand === null) {
                            throw new CommandParsingException($message);
                        }
                        throw new SubcommandParsingException(
                            $subcommand, $message
                        );
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
                                $message = 'Option require argument';
                                if ($subcommand === null) {
                                    throw new CommandParsingException($message);
                                }
                                throw new SubcommandParsingException(
                                    $subcommand, $message
                                );
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
                $optionName = $element;
                if (strpos($element, '=') !== false) {
                    list($optionName, $optionArgument) =
                        explode('=', $element, 2);
                }
                $optionName = substr($optionName, 2);
                if (isset($optionConfigs[$optionName]) === false) {
                    $message = "Unknown option $optionName";
                    if ($subcommand === null) {
                        throw new CommandParsingException($message);
                    }
                    throw new SubcommandParsingException(
                        $subcommand, $message
                    );
                }
                $option = $optionConfigs[$optionName];
                if ($option->hasArgument() === 1) {
                    if ($optionArgument === null) {
                        ++$index;
                        if ($index >= $count) {
                            $message =
                                "Option $optionName requires an argument";
                            if ($subcommand === null) {
                                throw new CommandParsingException($message);
                            }
                            throw new SubcommandParsingException(
                                $subcommand, $message
                            );
                        }
                        $optionArgument = $argv[$index];
                    }
                } elseif ($option->hasArgument() === -1) {
                    if ($optionArgument !== true) {
                        $message =
                            "Option $optionName do not accept an argument";
                        if ($subcommand === null) {
                            throw new CommandParsingException($message);
                        }
                        throw new SubcommandParsingException(
                            $subcommand, $message
                        );
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
        $hasMagicOption = static::hasMagicOption(
            isset($result['global_options']) ? $result['global_options'] : null,
            $subcommand,
            isset($result['options']) ? $result['options'] : null,
            $commandConfig
        );
        if (isset($result['global_options'])) {
            $globalOptionConfigs = $commandConfig->getOptions();
            $globalMutuallyExclusiveOptionGroupConfigs =
                $commandConfig->getMutuallyExclusiveOptionGroups();
            self::checkOptions(
                null,
                $result['global_options'],
                $globalOptionConfigs,
                $globalMutuallyExclusiveOptionGroupConfigs,
                $hasMagicOption
            );
        }
        if (isset($result['options'])) {
            $mutuallyExclusiveOptionGroupConfigs =
                $commandConfig->getMutuallyExclusiveOptionGroups($subcommand);
            self::checkOptions(
                $subcommand,
                $result['options'],
                $optionConfigs,
                $mutuallyExclusiveOptionGroupConfigs,
                $hasMagicOption
            );
        }
        if ($subcommand !== null) {
            $result['subcommand'] = $subcommand;
        }
        if ($isGlobal || $hasMagicOption) {
            if ($hasMagicOption) {
                $result['arguments'] = $arguments;
            }
            return $result;
        }
        $result['arguments'] = [];
        $argumentConfigs = null;
        if ($commandConfig->isSubcommandEnabled()) {
            $argumentConfigs = $commandConfig->getArguments($subcommand);
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
                } else {
                    $result['arguments'][] = $arguments[$argumentIndex];
                }
            } else {
                $argumentConfig = end($argumentConfigs);
                if ($argumentConfig->isRepeatable()) {
                    $result['arguments'][count($result['arguments']) - 1][] =
                        $arguments[$argumentIndex];
                } else {
                    $message = 'Argument number error.';
                    if ($subcommand === null) {
                        throw new CommandParsingException($message);
                    }
                    throw new SubcommandParsingException(
                        $subcommand, $message
                    );
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
                $message = 'Argument number error.';
                if ($subcommand === null) {
                    throw new CommandParsingException($message);
                }
                throw new SubcommandParsingException(
                    $subcommand, $message
                );
            }
        }
        return $result;
    }

    protected static function hasMagicOption(
        array $globalOptions = null,
        $subcommand,
        array $options = null,
        $commandConfig
    ) {
        if ($commandConfig->isSubcommandEnabled()) {
            if ($globalOptions !== null) {
                foreach ($globalOptions as $key => $value) {
                    if (in_array($key, array('help', 'version'))) {
                        return true;
                    }
                }
            }
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if (in_array($key, array('help'))) {
                        return true;
                    }
                }
            }
        } else {
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if (in_array($key, array('help', 'version'))) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function checkOptions(
        $subcommand,
        array $options,
        array $optionConfigs,
        array $mutuallyExclusiveOptionGroupConfigs = null,
        $hasMagicOption
    ) {
        foreach ($optionConfigs as $name => $option) {
            if ($option->isRequired()) {
                if (isset($options[$name])) {
                    continue;
                }
                if ($hasMagicOption === false) {
                    $message = "Option $name is required";
                    if ($subcommand === null) {
                        throw new CommandParsingException($message);
                    }
                    throw new SubcommandParsingException(
                        $subcommand, $message
                    );
                }
            }
        }
        foreach ($options as $name => $value) {
            $option = $optionConfigs[$name];
            $values = $option->getValues();
            if ($option->getValues() !== null) {
                if (in_array($value, $values, true) === false) {
                    $message = "The value of Option '$name' is not valid";
                    if ($subcommand === null) {
                        throw new CommandParsingException($message);
                    }
                    throw new SubcommandParsingException(
                        $subcommand, $message
                    );
                }
            }
        }
        if ($mutuallyExclusiveOptionGroupConfigs !== null) {
            foreach($mutuallyExclusiveOptionGroupConfigs as $groupConfig) {
                $optionKey = null;
                foreach ($groupConfig->getOptions() as $option) {
                    $key = $option->getName();
                    if ($key === null) {
                        $key = $option->getShortName();
                    }
                    if (isset($options[$key])) {
                        if ($optionKey !== null && $optionKey !== $key) {
                            $message = "Mutually exclusive option conflict"
                                . "($optionKey & $key)";
                            if ($subcommand === null) {
                                throw new CommandParsingException($message);
                            }
                            throw new SubcommandParsingException(
                                $subcommand, $message
                            );
                        }
                        $optionKey = $key;
                    }
                }
                if ($groupConfig->isRequired() && $hasOption === false) {
                    if ($hasMagicOption === false) {
                        $message = 'Option group is required';
                        if ($subcommand === null) {
                            throw new CommandParsingException($message);
                        }
                        throw new SubcommandParsingException(
                            $subcommand, $message
                        );
                    }
                }
            }
        }
    }
}
