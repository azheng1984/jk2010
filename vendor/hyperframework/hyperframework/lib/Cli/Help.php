<?php
namespace Hyperframework\Cli;

class Help {
    private $app;
    private $config;
    private $hasOptionDescription;

    public function __construct($app) {
        $this->app = $app;
        $this->config = $this->app->getCommandConfig();
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
    }

    private function renderUsage() {
        $name = (string)$this->config->get('name');
        if ($name === '') {
            throw new Exception;
        }
        if ($config->hasMultipleCommands()) {
            echo 'Usage: ';
            if ($this->app->hasSubcommand() === false) {
                echo $name;
                $this->renderSubcommands();
            } else {
                $subcommand = $app->getSubcommand();
                echo $name, ' ', $subcommand;
            }
            //check option count
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactOptions();
            } else {
                echo ' [opitons]';
            }
            if ($this->app->hasSubcommand() === false) {
                echo ' <command>', PHP_EOL;
            } else {
                echo ' ';
                $this->renderCompactArguments()
                echo PHP_EOL;
            }
        } else {
            echo 'Usage: ', $name;
            if ($this->hasOptionDescription() === false) {
                $this->renderCompactedOptions();
            } else {
                echo '[opitons]';
            }
            $this->renderCompactArguments();
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
