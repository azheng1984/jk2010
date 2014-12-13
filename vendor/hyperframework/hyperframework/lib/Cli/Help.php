<?php
namespace Hyperframework\Cli;

class Help {
    private $app;
    private $config;
    private $hasOptionDescription;
    private $subcommand;
    private $subcommandCount;
    private $options;
    private $optionCount;
    private $arguments;
    private $argumentCount;
    private $usageLineLength = 0;
    private $usageIndent = 10;

    public function __construct($app) {
        $this->app = $app;
        $this->config = $app->getCommandConfig();
        if ($config->isSubcommandEnabled()) {
            $this->subcommand = $app->getSubcommand();
        }
    }

    public function render() {
        $this->renderUsage();
        if ($this->hasOptionDescription()) {
            $this->renderOptions();
        }
        if ($this->config->isSubcommandEnabled()
            && $app->hasSubcommand() === false
        ) {
            $this->renderSubcommands();
        }
    }

    private function renderUsageElement($element) {
        $length = strlen($element);
        if ($length === 0) {
            return;
        }
        if ($this->usageLineLength > 6
            && $length + $this->usageLineLength > 80
        ) {
            echo PHP_EOL, '      ';
            $this->usageLineLength = 6;
        }
        if ($this->usageLineLength !== 6 && $element[0] !== '|') {
            echo ' ';
        }
        echo $element;
        $this->usageLineLength += $length;
    }

    protected function renderUsage() {
        $name = $this->config->getName();
        if (strlen($name . $this->subcommand) < 4) {
            $this->usageIndent = strlen($name . $this->subcommand) + 6;
        } else {
            $this->usageIndent = 10;
        }
        $this->renderUsageElement('Usage: ' . $name);
        if ($this->subcommand !== null) {
            $this->renderUsageElement($this->subcommand);
        }
        $options = $this->config->getOptions($this->subcommand);
        $optionCount = count($options);
        if ($optionCount > 0) {
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                if ($optionCount === 1) {
                    $this->renderUsageElement('[option]');
                } else {
                    $this->renderUsageElement('[options]');
                }
            }
        }
        if ($this->config->isSubcommandEnabled()
            && $this->subcommand === null
        ) {
            $this->renderUsageElement('<command>');
        } elseif (count($this->config->getArguments($this->subcommand)) > 0) {
            $this->renderArguments();
        }
        echo PHP_EOL;
    }

    private function renderArguments() {
        $arguments = $this->config->getArguments($this->subcommand);
        foreach ($arguments as $argument) {
            $name = '<' . $argument->getName() . '>';
            if ($argument->isRepeatable()) {
                $name .= '...';
            }
            if ($argument->isOptional()) {
                $name = '[' . $name . ']';
            }
            $this->renderUsageElement($name);
        }
    }

    private function getOptionPattern(
        $option, $isCompact, $isRequired = null
    ) {
        $result = '';
        $shortName = (string)$option->getShortName();
        if ($shortName !== '') {
            $result .= '-' . $shortName;
        }
        $name = (string)$option->getName();
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
        $hasArgument = $option->hasArgument();
        if ($hasArgument !== -1) {
            $values = $option->getValues();
            if ($values !== null) {
                $argumentPattern = implode('|', $values);
                if ($name !== '') {
                    $argumentPattern = '(' . $argumentPattern . ')';
                }
            } else {
                $argumentPattern = $option->getArgumentPattern();
            }
            if ($hasArgument === 0) {
                if ($name === '') {
                    $result .= '[', $argumentPattern, ']';
                } else {
                    $result .= '[=', $argumentPattern, ']'
                }
            } else {
                if ($name === '') {
                    if ($isCompact === false) {
                        $result .= ' ';
                    }
                    $result .= $argumentPattern;
                } else {
                    $result .= '=', $argumentPattern;
                }
            }
        }
        if ($isCompact) {
            if ($name !== ''
                && $shortName !== ''
                && ($isRequired === true || $option->isRequired())
            ) {
                $result = '(' . $result . ')';
            } else {
                $result = '[' . $result . ']';
            }
        }
        return $result;
    }

    private function renderCompactOptions() {
        $options = $this->config->getOptions($this->subcommand);
        $includedOptions = [];
        foreach ($options as $option) {
            $name = (string)$option->getName();
            $shortName = (string)$option->getShortName();
            $key = $name === '' ? $shortName : $name;
            if (isset($includedOptions[$key])) {
                continue;
            }
            $includedOptions[$key] = true;
            $optionGroup =
                $this->config->getMutuallyExclusiveOptionGroupByOption($option);
            $hasBrackets = false;
            if ($name !== '' && $shortName !== '') {
                $hasBrackets = true;
            }
            $isRequired = $option->isRequired();
            if ($optionGroup !== null) {
                $isReqired = $optionGroup->isRequired();
                $mutuallyExclusiveOptions = $optionGroup->getOptions();
                $count = count($mutuallyExclusiveOptions);
                $index = 0;
                $length = 0;
                $buffer = '';
                if (count($mutuallyExclusiveOptions) > 1) {
                    if ($index === 0) {
                        if ($isRequired === false) {
                            $buffer = '[';
                        } else {
                            $buffer = '(';
                        }
                    } else {
                        $buffer = '';
                    }
                    foreach ($mutuallyExclusiveOptions
                        as $mutuallyExclusiveOption
                    ) {
                        $element = $this->getOptionPattern(
                            $mutuallyExclusiveOption, true, true
                        );
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
                    }
                    if ($isRequired === false) {
                        $buffer .= ']';
                    } else {
                        $buffer .= ')';
                    }
                    $this->renderUsageElement($buffer);
                    continue;
                }
            }
            $this->renderUsageElement($this->getOptionPattern(
                $mutuallyExclusiveOption, true, $isRequired
            ));
        }
    }

    protected function renderOptions() {
        $options = $this->config->getOptions($this->subcommand);
        $count = count($options);
        if ($count === 0) {
            return;
        }
        if ($count === 1) {
            echo 'Option:';
        } else {
            echo 'Options:';
        }
        echo PHP_EOL;
        $patterns = [];
        $descriptions = [];
        foreach ($options as $option) {
            $patterns[] = $this->getOptionPattern($option, false);
            $descriptions[] = (string)$option->getDescription();
        }
        $this->renderList($patterns, $descriptions);
    }

    private function renderList($names, $descriptions) {
        $maxLength = null;
        $count = 0;
        foreach ($names as $name) {
            $length = strlen($name);
            if ($length > $maxLength) {
                if ($length < 27) {
                    $maxLength = $length;
                } else {
                    ++$count;
                }
            }
        }
        if ($count > count($names) / 2) {
        }
        $count = count($names);
        if ($count === 2) {
        }
        for ($index = 0; $index < $count; ++$index) {
            $name = $names[$index];
            echo ' ', $name;
            $description = $descriptions[$index];
            if ($description !== '') {
                $length = strlen($name);
                if ($length > 27) {
                    if ($length + strlen($description) + 3 <= 80) {
                        $length = $maxLength;
                    } else {
                        echo PHP_EOL;
                        $length = 0;
                    }
                }
                echo str_repeat(' ', $maxLength - $length + 2),
                    $description, PHP_EOL;
            }
        }
    }

    protected function renderSubcommands() {
        $subcommands = $this->config->getSubcommands();
        $count = count($subcommands);
        if ($count === 0) {
            return;
        }
        if ($count === 1) {
            echo 'Command:';
        } else {
            echo 'Commands:';
        }
        echo PHP_EOL;
        $descriptions = [];
        foreach ($subcommands as $subcommand) {
            $descriptions[] = (string)$this->getDescription($subcommand);
        }
        $this->renderList($subcommands, $descriptions);
    }

    private function hasOptionDescription() {
        if ($this->hasOptionDescription === null) {
            $subcommand = $this->app->getSubcommand();
            $options = $this->config->getOptions($subcommand);
            foreach ($options as $option) {
                if ((string)$option->getDescription() !== '') {
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
