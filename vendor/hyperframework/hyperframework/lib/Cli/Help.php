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
    private $currentOutputLineLength = 0;

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
        //getArgumentPattern;
        //getOptionPattern
        // echo [article] [(<key>=<value>)...]
        // echo ASDR <arg1>=<arg2>
        // [-d,--dd] ((-a,--aa[=dis])|(-c,--cc))
    }

    private function outputUsage($segment) {
    }

    protected function renderUsage() {
        echo 'Usage: ', $this->config->getName();
        if ($sthis->subcommand !== null) {
            echo $this->subcommand;
        }
        $options = $this->config->getOptions($this->subcommand);
        $optionCount = count($options);
        if ($optionCount > 0) {
            echo ' ';
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                if ($optionCount === 1) {
                    echo '[option]';
                } else {
                    echo '[opitons]';
                }
            }
        }
        if ($this->config->isSubcommandEnabled()
            && $this->subcommand === null
        ) {
            echo ' <command>', PHP_EOL;
            return;
        }
        if (count($this->config->getArguments($this->subcommand)) > 0) {
            echo ' ';
            $this->renderArguments();
            echo PHP_EOL;
        }
    }

    protected function renderArguments() {
        $arguments = $this->config->getArguments($this->subcommand);
        $names = [];
        foreach ($arguments as $argument) {
            $name = '<' . $argument->getName() . '>';
            if ($argument->isRepeatable()) {
                $name .= '...';
            }
            if ($argument->isOptional()) {
                $name = '[' . $name . ']';
            }
            $names[] = $name;
        }
        echo implode(' ', $name);
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
            $argumentName = (string)$option->getArgumentName();
            if ($argumentName === '') {
                $values = $option->getValues();
                if ($values !== null && count($values) > 0) {
                    $argumentName = '(' . implode('|', $values) . ')';
                }
            } else {
                $argumentName = '<' . $argumentName . '>';
            }
            if ($argumentName === '') {
                $argumentName = '<arg>';
            }
            if ($hasArgument === 0) {
                if ($name === '') {
                    $result .= '[', $argumentName, ']';
                } else {
                    $result .= '[=', $argumentName, ']'
                }
            } else {
                if ($name === '') {
                    if ($isCompact === false) {
                        $result .= ' ';
                    }
                    $result .= $argumentName;
                } else {
                    $result .= '=', $argumentName;
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

    protected function renderCompactOptions() {
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
                if (count($mutuallyExclusiveOptions) > 1) {
                    foreach ($mutuallyExclusiveOptions
                        as $mutuallyExclusiveOption
                    ) {
                        $output .= '|' . $this->getOptionPattern(
                            $mutuallyExclusiveOption, true, true
                        );
                    }
                    if ($isRequired === false) {
                        echo ' [' . $output . ']';
                    } else {
                        echo ' (' . $output . ')';
                    }
                    continue;
                }
            }
            echo ' ' . $this->getOptionPattern(
                $mutuallyExclusiveOption, true, $isRequired
            );
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
            echo 'Options:'
        }
        echo PHP_EOL;
        foreach ($options as $option) {
            echo $this->getOptionPattern($option, false);
            //check max length & 80 edge
            $description = $this->getDescription();
            if ($description !== '') {
                echo '  ', $description;
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
        foreach ($subcommands as $subcommand) {
            echo $subcommand, PHP_EOL;
        }
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
