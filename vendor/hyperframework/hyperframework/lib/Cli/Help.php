<?php
namespace Hyperframework\Cli;

class Help {
    private $commandConfig;
    private $hasOptionDescription;
    private $subcommandName;
    private $usageLineLength = 0;
    private $usageIndent;

    public function __construct($app) {
        $this->commandConfig = $app->getCommandConfig();
        if ($this->commandConfig->isSubcommandEnabled()) {
            $this->subcommandName = $app->getSubcommandName();
        }
    }

    public function render() {
        $this->renderUsage();
        if ($this->hasOptionDescription()) {
            $this->renderOptions();
        }
        if ($this->commandConfig->isSubcommandEnabled()
            && $this->subcommandName === null
        ) {
            $this->renderSubcommands();
        }
    }

    protected function renderUsage() {
        $name = $this->commandConfig->getName();
        $prefix = $name;
        if ($this->subcommandName !== null) {
            $prefix .= ' ' . $this->subcommandName;
        }
        if (strlen($prefix) < 7) {
            $this->usageIndent = strlen($prefix) + 8;
        } else {
            $this->usageIndent = 11;
        }
        $this->renderUsageElement('Usage: ' . $name);
        if ($this->subcommandName !== null) {
            $this->renderUsageElement($this->subcommandName);
        }
        $optionConfigs = $this->commandConfig
            ->getOptionConfigs($this->subcommandName);
        $optionCount = count($optionConfigs);
        if ($optionCount > 0) {
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                $this->renderUsageElement('[options]');
            }
        }
        if ($this->commandConfig->isSubcommandEnabled()
            && $this->subcommandName === null
        ) {
            $this->renderUsageElement('<subcommand>');
        } elseif (count($this->commandConfig
            ->getArgumentConfigs($this->subcommandName)) > 0
        ) {
            $this->renderArguments();
        }
        echo PHP_EOL;
    }

    protected function renderOptions() {
        $optionConfigs = $this->commandConfig
        ->getOptionConfigs($this->subcommandName);
        $count = count($optionConfigs);
        if ($count === 0) {
            return;
        }
        echo PHP_EOL, 'Options:', PHP_EOL;
        $patterns = [];
        $descriptions = [];
        $includedOptionConfigs = [];
        foreach ($optionConfigs as $optionConfig) {
            if (in_array($optionConfig, $includedOptionConfigs, true)) {
                continue;
            }
            $includedOptionConfigs[] = $optionConfig;
            $patterns[] = $this->getOptionPattern($optionConfig, false);
            $descriptions[] = (string)$optionConfig->getDescription();
        }
        $this->renderList($patterns, $descriptions);
    }

    protected function renderSubcommands() {
        $names = $this->commandConfig->getSubcommandNames();
        $count = count($names);
        if ($count === 0) {
            return;
        }
        echo PHP_EOL, 'Subcommands:', PHP_EOL;
        $descriptions = [];
        foreach ($names as $name) {
            $descriptions[] = (string)$this->commandConfig->getDescription(
                $name
            );
        }
        $this->renderList($names, $descriptions);
    }

    protected function hasOptionDescription() {
        if ($this->hasOptionDescription === null) {
            $optionConfigs = $this->commandConfig
            ->getOptionConfigs($this->subcommandName);
            foreach ($optionConfigs as $optionConfig) {
                if ((string)$optionConfig->getDescription() !== '') {
                    $this->hasOptionDescription = true;
                }
            }
            if ($this->hasOptionDescription !== true) {
                $this->hasOptionDescription = false;
            }
        }
        return $this->hasOptionDescription;
    }

    private function renderUsageElement($element) {
        $length = strlen($element);
        if ($length === 0) {
            return;
        }
        if ($this->usageLineLength > $this->usageIndent
            && $length + $this->usageLineLength > 80
        ) {
            echo PHP_EOL, str_repeat(' ', $this->usageIndent);
            $this->usageLineLength = $this->usageIndent;
        } elseif ($this->usageLineLength !== 0
            && $element[0] !== '|'
        ) {
            echo ' ';
        }
        echo $element;
        $this->usageLineLength += $length;
    }

    private function renderArguments() {
        $argumentConfigs = $this->commandConfig
            ->getArgumentConfigs($this->subcommandName);
        foreach ($argumentConfigs as $argumentConfig) {
            $name = '<' . $argumentConfig->getName() . '>';
            if ($argumentConfig->isRepeatable()) {
                $name .= '...';
            }
            if ($argumentConfig->isRequired() === false) {
                $name = '[' . $name . ']';
            }
            $this->renderUsageElement($name);
        }
    }

    private function getOptionPattern(
        $optionConfig, $isCompact, $isRequired = null
    ) {
        $result = '';
        $name = $optionConfig->getName();
        if (strlen($name) === 1) {
            $shortName = $name;
            $name = null;
        } else {
            $shortName = $optionConfig->getShortName();
        }
        if ($shortName !== null) {
            $result .= '-' . $shortName;
        }
        if ($name !== null) {
            if ($shortName !== null) {
                if ($isCompact) {
                    $result .= '|';
                } else {
                    $result .= ', ';
                }
            }
            $result .= '--' . $name;
        }
        $argumentConfig = $optionConfig->getArgumentConfig();
        if ($argumentConfig !== null) {
            $values = $argumentConfig->getValues();
            if ($values !== null) {
                $argumentPattern = '(' . implode('|', $values) . ')';
            } else {
                $argumentPattern = '<' . $argumentConfig->getName() . '>';
            }
            if ($argumentConfig->isRequired()) {
                if ($name === null) {
                    $result .= ' ' . $argumentPattern;
                } else {
                    $result .= '='. $argumentPattern;
                }
            } else {
                if ($name === null) {
                    $result .= '[' . $argumentPattern . ']';
                } else {
                    $result .= '[=' . $argumentPattern . ']';
                }
            }
        }
        if ($isCompact) {
            if ($isRequired === true || $optionConfig->isRequired()) {
                if (($name !== null && $shortName !== null)
                    || ($shortName !== null && $optionConfig->isRequired())
                ) {
                    $result = '(' . $result . ')';
                }
            } else {
                $result = '[' . $result . ']';
            }
        }
        return $result;
    }

    private function renderCompactOptions() {
        $optionConfigs = $this->commandConfig
            ->getOptionConfigs($this->subcommandName);
        $includedOptionConfigs = [];
        foreach ($optionConfigs as $optionConfig) {
            $name = $optionConfig->getName();
            $shortName = $optionConfig->getShortName();
            if (in_array($optionConfig, $includedOptionConfigs, true)) {
                continue;
            }
            $includedOptionConfigs[] = $optionConfig;
            $mutuallyExclusiveOptionGroupConfig =
                $this->getMutuallyExclusiveOptionGroupConfigByOptionConfig(
                    $optionConfig
                );
            $hasBrackets = false;
            if ($name !== null && $shortName !== null) {
                $hasBrackets = true;
            }
            $isRequired = $optionConfig->isRequired();
            if ($mutuallyExclusiveOptionGroupConfig !== null) {
                $isReqired = $mutuallyExclusiveOptionGroupConfig->isRequired();
                $mutuallyExclusiveOptionConfigs =
                    $mutuallyExclusiveOptionGroupConfig->getOptionConfigs();
                $count = count($mutuallyExclusiveOptionConfigs);
                $index = 0;
                $length = 0;
                $buffer = '';
                if ($count > 1) {
                    if ($index === 0) {
                        if ($isRequired) {
                            $buffer = '(';
                        } else {
                            $buffer = '[';
                        }
                    } else {
                        $buffer = '';
                    }
                    foreach ($mutuallyExclusiveOptionConfigs
                        as $mutuallyExclusiveOptionConfig
                    ) {
                        $element = $this->getOptionPattern(
                            $mutuallyExclusiveOptionConfig, true, true
                        );
                        $includedOptionConfigs[]
                            = $mutuallyExclusiveOptionConfig;
                        if (strlen($element + $buffer) > 70) {
                            if ($index !== 0) {
                                $this->renderUsageElement($buffer);
                                $buffer = '';
                            }
                        }
                        if ($index !== 0) {
                            $buffer .= '|';
                        }
                        $buffer .= $element;
                        ++$index;
                    }
                    if ($isRequired) {
                        $buffer .= ')';
                    } else {
                        $buffer .= ']';
                    }
                    $this->renderUsageElement($buffer);
                    continue;
                }
            }
            $this->renderUsageElement($this->getOptionPattern(
                $optionConfig, true, $isRequired
            ));
        }
    }

    private function renderList($names, $descriptions) {
        $maxLength = 0;
        $count = 0;
        $index = 0;
        $descriptionCount = 0;
        foreach ($names as $name) {
            if ((string)$descriptions[$index] === '') {
                $index++;
                continue;
            }
            ++$descriptionCount;
            $length = strlen($name);
            if ($length > $maxLength) {
                if ($length < 28) {
                    $maxLength = $length;
                    ++$count;
                }
            }
            ++$index;
        }
        $isNewLine = false;
        if ($count === 0 || $count / $descriptionCount <= 0.5) {
            $isNewLine = true;
        }
        $count = count($names);
        for ($index = 0; $index < $count; ++$index) {
            $name = $names[$index];
            echo ' ', $name;
            $description = $descriptions[$index];
            if ($description !== '') {
                if ($isNewLine) {
                    echo PHP_EOL, '     ', $description, PHP_EOL;
                    continue;
                }
                $length = strlen($name);
                if ($length > 27) {
                    echo PHP_EOL;
                    $length = -1;
                }
                echo str_repeat(' ', $maxLength - $length + 2),
                    $description, PHP_EOL;
            } else {
                echo PHP_EOL;
            }
        }
    }

    private function getMutuallyExclusiveOptionGroupConfigByOptionConfig(
        $optionConfig
    ) {
        $configs = $this->commandConfig->getMutuallyExclusiveOptionGroupConfigs(
            $this->subcommandName
        );
        foreach ($configs as $config) {
            if (in_array($optionConfig, $config->getOptionConfigs(), true)) {
                return $config;
            }
        }
    }
}
