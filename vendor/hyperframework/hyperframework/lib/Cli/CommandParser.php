<?php
namespace Hyperframework\Cli;

class CommandParser {
    public static function parse($commandConfig, array $argv = null) {
        if ($argv === null) {
            $argv = $_SERVER['argv'];
        }
        $optionConfigs = $commandConfig->getOptionConfigs();
        $result = [];
        $subcommandName = null;
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
                    if ($commandConfig->hasSubcommand($element) === false) {
                        throw new CommandParsingException(
                            "Subcommand '$element' does not exist."
                        );
                    }
                    $isGlobal = false;
                    $subcommandName = $element;
                    $result['subcommand_name'] = $element;
                    $result['options'] = [];
                    $optionConfigs = $commandConfig->getOptionConfigs($element);
                    $optionType = 'options';
                } else {
                    $arguments[] = $element;
                }
                continue;
            }
            if ($element === '--') {
                if ($isGlobal) {
                    throw new CommandParsingException(
                        "Option '--' is not allowed."
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
                        $message = "Option '$optionName' is not allowed.";
                        throw new CommandParsingException(
                            $message, $subcommandName
                        );
                    }
                    $optionArgument = true;
                    $optionConfig = $optionConfigs[$optionName];
                    $optionArgumentConfig = $optionConfig->getArgumentConfig();
                    $hasArgument = -1;
                    if ($optionArgumentConfig !== null) {
                        $hasArgument = $optionArgumentConfig->isRequired() ?
                            1 : 0;
                    }
                    if ($hasArgument === 0) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        }
                    } elseif ($hasArgument === 1) {
                        if ($length > 2) {
                            $optionArgument = substr($element, 1 + $charIndex);
                        } else {
                            ++$index;
                            if ($index >= $count) {
                                $message = 'Option \''
                                    . $optionName . '\' must have an argument.';
                                throw new CommandParsingException(
                                    $message, $subcomman
                                );
                            }
                            $optionArgument = $argv[$index];
                        }
                    }
                    if ($optionConfig->isRepeatable()) {
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
                    $optionFullName = $optionConfig->getName();
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
                    $message = "Unknown option '$optionName'.";
                    throw new CommandParsingException(
                        $message, $subcommandName
                    );
                }
                $optionConfig = $optionConfigs[$optionName];
                $optionArgumentConfig = $optionConfig->getArgumentConfig();
                $hasArgument = -1;
                if ($optionArgumentConfig !== null) {
                    $hasArgument = $optionArgumentConfig->isRequired() ?
                        1 : 0;
                }
                if ($hasArgument === 1) {
                    if ($optionArgument === null) {
                        ++$index;
                        if ($index >= $count) {
                            $message =
                                "Option '$optionName' must have an argument.";
                            throw new CommandParsingException(
                                $message, $subcommandName
                            );
                        }
                        $optionArgument = $argv[$index];
                    }
                } elseif ($hasArgument === -1) {
                    if ($optionArgument !== true) {
                        $message =
                            "Option '$optionName' must not have an argument.";
                        throw new CommandParsingException(
                            $message, $subcommandName
                        );
                    }
                }
                if ($optionConfig->isRepeatable()) {
                    if (isset($result['options'][$optionName])) {
                        $result[$optionType][$optionName][] = $optionArgument;
                    } else {
                        $result[$optionType][$optionName] = [$optionArgument];
                    }
                } else {
                    $result[$optionType][$optionName] = $optionArgument;
                }
                $optionShortName = $optionConfig->getShortName();
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
            $subcommandName,
            isset($result['options']) ? $result['options'] : null,
            $commandConfig
        );
        if (isset($result['global_options'])) {
            $globalOptionConfigs = $commandConfig->getOptionConfigs();
            $globalMutuallyExclusiveOptionGroupConfigs =
                $commandConfig->getMutuallyExclusiveOptionGroupConfigs();
            self::checkOptions(
                null,
                $result['global_options'],
                $globalOptionConfigs,
                $globalMutuallyExclusiveOptionGroupConfigs,
                $hasMagicOption
            );
        }
        if (isset($result['options'])) {
            $mutuallyExclusiveOptionGroupConfigs = $commandConfig
                ->getMutuallyExclusiveOptionGroupConfigs($subcommandName);
            self::checkOptions(
                $subcommandName,
                $result['options'],
                $optionConfigs,
                $mutuallyExclusiveOptionGroupConfigs,
                $hasMagicOption
            );
        }
        if ($subcommandName !== null) {
            $result['subcommand_name'] = $subcommandName;
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
            $argumentConfigs = $commandConfig->getArgumentConfigs(
                $subcommandName
            );
        } else {
            $argumentConfigs = $commandConfig->getArgumentConfigs();
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
                if ($argumentConfig !== false 
                    && $argumentConfig->isRepeatable()
                ) {
                    $result['arguments'][count($result['arguments']) - 1][] =
                        $arguments[$argumentIndex];
                } else {
                    throw new CommandParsingException(
                        'Number of arguments error.', $subcommandName
                    );
                }
            }
        }
        $count = 0;
        foreach ($argumentConfigs as $argumentConfig) {
            if ($argumentConfig->isRequired() === false) {
                break;
            }
            ++$count;
            if ($count > $argumentCount) {
                $message = 'Number of arguments error.';
                throw new CommandParsingException($message, $subcommandName);
            }
        }
        return $result;
    }

    private static function hasMagicOption(
        array $globalOptions = null,
        $subcommandName,
        array $options = null,
        $commandConfig
    ) {
        if ($commandConfig->isSubcommandEnabled()) {
            if ($globalOptions !== null) {
                foreach ($globalOptions as $key => $value) {
                    if (in_array($key, ['help', 'version'])) {
                        return true;
                    }
                }
            }
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if ($key === 'help') {
                        return true;
                    }
                }
            }
        } else {
            if ($options !== null) {
                foreach ($options as $key => $value) {
                    if (in_array($key, ['help', 'version'])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private static function checkOptions(
        $subcommandName,
        array $options,
        array $optionConfigs,
        array $mutuallyExclusiveOptionGroupConfigs = null,
        $hasMagicOption
    ) {
        foreach ($optionConfigs as $name => $optionConfig) {
            if ($optionConfig->isRequired()) {
                if (isset($options[$name])) {
                    continue;
                }
                if ($hasMagicOption === false) {
                    $message = "Option '$name' is required.";
                    throw new CommandParsingException(
                        $message, $subcommandName
                    );
                }
            }
        }
        foreach ($options as $name => $value) {
            $optionConfig = $optionConfigs[$name];
            $argumentConfig = $optionConfig->getArgumentConfig();
            if ($argumentConfig !== null) {
                $values = $argumentConfig->getValues();
                if ($values !== null) {
                    if (in_array($value, $values, true) === false) {
                        $message = "The value of option '$name' is invalid.";
                        throw new CommandParsingException(
                            $message, $subcommandName
                        );
                    }
                }
            }
        }
        if ($mutuallyExclusiveOptionGroupConfigs !== null) {
            foreach($mutuallyExclusiveOptionGroupConfigs as $groupConfig) {
                $optionKey = null;
                $optionKeys = [];
                foreach ($groupConfig->getOptionConfigs() as $optionConfig) {
                    $key = $optionConfig->getName();
                    if ($key === null) {
                        $key = $optionConfig->getShortName();
                    }
                    if (isset($options[$key])) {
                        if ($optionKey !== null && $optionKey !== $key) {
                            $message = "The '$optionKey' and '$key'"
                                . " options are mutually exclusive.";
                            throw new CommandParsingException(
                                $message, $subcommandName
                            );
                        }
                        $optionKey = $key;
                        $optionKeys[] = "'" . $key . "'";
                    }
                }
                if ($groupConfig->isRequired() && $optionKey === null) {
                    if ($hasMagicOption === false && count($optionKeys) !== 0) {
                        $message = "One of option '"
                            . implode(', ', $optionKeys) . "' is required.";
                        throw new CommandParsingException(
                            $message, $subcommandName
                        );
                    }
                }
            }
        }
    }
}
