<?php
namespace Hyperframework\Cli;

class Help {
    private $commandConfig;
    private $hasOptionDescription;
    private $subcommand;
    private $usageLineLength = 0;
    private $usageIndent;

    public function __construct($app) {
        $this->commandConfig = $app->getCommandConfig();
        if ($this->commandConfig->isSubcommandEnabled()) {
            $this->subcommand = $app->getSubcommand();
        }
    }

    public function render() {
        $this->renderUsage();
        if ($this->hasOptionDescription()) {
            $this->renderOptions();
        }
        if ($this->commandConfig->isSubcommandEnabled()) {
            $this->renderSubcommands();
        }
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

    protected function renderUsage() {
        $name = $this->commandConfig->getName();
        $prefix = $name;
        if ($this->subcommand !== null) {
            $prefix .= ' ' . $this->subcommand;
        }
        if (strlen($prefix) < 7) {
            $this->usageIndent = strlen($prefix) + 8;
        } else {
            $this->usageIndent = 11;
        }
        $this->renderUsageElement('Usage: ' . $name);
        if ($this->subcommand !== null) {
            $this->renderUsageElement($this->subcommand);
        }
        $optionConfigs = $this->commandConfig
            ->getOptionConfigs($this->subcommand);
        $optionCount = count($optionConfigs);
        if ($optionCount > 0) {
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                $this->renderUsageElement('[options]');
            }
        }
        if ($this->commandConfig->isSubcommandEnabled()
            && $this->subcommand === null
        ) {
            $this->renderUsageElement('<command>');
        } elseif (count($this->commandConfig
            ->getArgumentConfigs($this->subcommand)) > 0
        ) {
            $this->renderArguments();
        }
        echo PHP_EOL;
    }

    private function renderArguments() {
        $argumentConfigs = $this->commandConfig
            ->getArgumentConfigs($this->subcommand);
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
        $shortName = (string)$optionConfig->getShortName();
        if ($shortName !== '') {
            $result .= '-' . $shortName;
        }
        $name = (string)$optionConfig->getName();
        if ($name !== '') {
            if ($shortName !== '') {
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
                if ($name === '') {
                    $result .= ' ' . $argumentPattern;
                } else {
                    $result .= '='. $argumentPattern;
                }
            } else {
                if ($name === '') {
                    $result .= '[' . $argumentPattern . ']';
                } else {
                    $result .= '[=' . $argumentPattern . ']';
                }
            }
        }
        if ($isCompact) {
            if ($isRequired === true || $optionConfig->isRequired()) {
                if (($name !== '' && $shortName !== '')
                    || ($shortName !== '' && $optionConfig->isRequired())
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
            ->getOptionConfigs($this->subcommand);
        $includedOptions = [];
        foreach ($optionConfigs as $optionConfig) {
            $name = (string)$optionConfig->getName();
            $shortName = (string)$optionConfig->getShortName();
            if (in_array($optionConfig, $includedOptions, true)) {
                continue;
            }
            $includedOptions[] = $optionConfig;
            $mutuallyExclusiveOptionGroupConfig = $this->commandConfig
                ->getMutuallyExclusiveOptionGroupConfigByOption($optionConfig);
            $hasBrackets = false;
            if ($name !== '' && $shortName !== '') {
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
                        $includedOptions[] = $mutuallyExclusiveOptionConfig;
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

    protected function renderOptions() {
        $optionConfigs = $this->commandConfig
            ->getOptionConfigs($this->subcommand);
        $count = count($optionConfigs);
        if ($count === 0) {
            return;
        }
        echo PHP_EOL, 'Options:', PHP_EOL;
        $patterns = [];
        $descriptions = [];
        $includedOptions = [];
        foreach ($optionConfigs as $optionConfig) {
            if (in_array($optionConfig, $includedOptions, true)) {
                continue;
            }
            $includedOptions[] = $optionConfig;
            $patterns[] = $this->getOptionPattern($optionConfig, false);
            $descriptions[] = (string)$optionConfig->getDescription();
        }
        $this->renderList($patterns, $descriptions);
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

    protected function renderSubcommands() {
        $subcommands = $this->commandConfig->getSubcommands();
        $count = count($subcommands);
        if ($count === 0) {
            return;
        }
        echo PHP_EOL, 'Commands:', PHP_EOL;
        $descriptions = [];
        foreach ($subcommands as $subcommand) {
            $descriptions[] = (string)$this->getDescription($subcommand);
        }
        $this->renderList($subcommands, $descriptions);
    }

    protected function hasOptionDescription() {
        if ($this->hasOptionDescription === null) {
            $optionConfigs = $this->commandConfig
                ->getOptionConfigs($this->subcommand);
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
}
