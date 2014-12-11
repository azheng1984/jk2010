<?php
namespace Hyperframework\Cli;

class Help {
    private $app;
    private $config;
    private $hasOptionDescription;

    public function __construct($app) {
        $this->app = $app;
        $this->config = $app->getCommandConfig();
    }

    public function render() {
        $this->renderUsage();
        if ($this->hasOptionDescription()) {
            $this->renderOptions();
        }
        if ($this->config->isMultipleCommandApp()
            && $app->hasSubcommand() === false
        ) {
            $this->renderSubcommands();
        }
    }

    private function hasOptionDescription() {
        $subcommand = $this->app->getSubcommand();
        $this->config->getOptions($subcommand);
    }

    private function renderUsage() {
        $name = $this->config->getName();
        if ($config->isSubcommandEnabled()) {
            $subcommand = $this->app->getSubcommand();
            echo 'Usage: ';
            if ($subcommand === null) {
                echo $name;
            } else {
                echo $name, ' ', $subcommand;
            }
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                if (count($this->config->getOptions($subcommand)) > 0) {
                    echo ' [opitons]';
                }
            }
            if ($subcommand === null) {
                echo ' <command>', PHP_EOL;
            } else {
                echo ' ';
                $this->renderArguments();
                echo PHP_EOL;
            }
        } else {
            echo 'Usage: ', $name;
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                echo '[opitons]';
            }
            $this->renderArguments();
            echo PHP_EOL;
        }
    }

    private function renderArguments() {
        $arguments = $this->config->get('arguments');
        if ($arguments === null) {
            return;
        }
        if (is_array($arguments)) {
            throw new Exception;
        }
        echo implode(', ', $arguments);
    }

    protected function renderCompactOptions() {
    }

    protected function renderSubcommands() {
        //only one command?
        echo 'Commands:';
        //read config folder
    }

    protected function renderOptions() {
        //only one option?
        echo 'Options:' . PHP_EOL;
        $arguments = $this->config->get('arguments');
        if ($arguments === null) {
            return;
        }
        if (is_array($arguments)) {
            throw new Exception;
        }
        echo implode(', ', $arguments);
    }
}
