<?php
namespace Hyperframework\Cli;

class Help {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function render() {
        $this->renderUsage();
        if ($this->hasOptionDescription()) {
            $this->renderOptions();
        }
        $config = $this->app->getCommandConfig();
        if ($config->isMultipleCommandApp()) {
            if ($app->hasSubcommand() === false) {
                $this->renderSubcommands();
            }
        }
    }

    private function hasOptionDescription() {
    }

    private function hasArgumentDescription() {
    }

    private function renderUsage() {
        $config = $this->app->getCommandConfig();
        $name = (string)$config->get('name');
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

    protected function renderCompactOptions() {
    }

    protected function renderSubcommands() {
        echo 'Commands:';
        //read config folder
    }

    protected function renderOptions() {
        echo 'Options:' . PHP_EOL;
    }
}
