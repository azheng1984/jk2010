<?php
namespace Hyperframework\Cli;

class Help {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function render() {
        if ($this->hasErrorMessage()) {
            $this->renderErrorHelp();
            return;
        }
        $this->renderFullHelp();
    }

    protected function hasErrorMessage() {
        return $this->errorMessage !== null;
    }

    protected function getErrorMessage() {
        return $this->errorMessage;
    }

    protected function renderFullHelp() {
        $this->renderUsage();
        $this->renderOptions();
    }

    protected function renderCompactedHelp() {
    }

    protected function renderUsage() {
        $commandConfig = $this->app->getCommandConfig();
        $name = $commandConfig->get('name');
        if ($commandConfig->hasMultipleCommands()) {
            if ($app->hasSubcommand() === false) {
                echo 'Usage: ', $name, ' [options] <command>' . PHP_EOL;
                $this->renderSubcommands();
            } else {
                $subcommand = $app->getSubcommand();
                echo 'Usage: ', $name, ' ', $subcommand, '[options] <arg>';
            }
        } else {
            echo 'Usage: ', $name, ' [options] <arg>' . PHP_EOL;
        }
    }

    protected function renderSubcommands() {
        echo 'Commands:';
    }

    protected function renderCompactedOptions() {
    }

    protected function renderOptions() {
        echo 'Options:' . PHP_EOL;
    }

    protected function renderErrorHelp() {
        $commandConfig = $this->app->getCommandConfig();
        $name = $commandConfig->get('name');
        echo (string)$this->errorMessage, PHP_EOL;
        $options = $commandConfig->get('options');
        $helpOption = null;
        if (isset($options['help'])) {
            $helpOption = '--help';
        } elseif (isset($options['-h'])) {
            $helpOption = 'h';
        }
        if ($helpOption !== null) {
            echo 'See \'', $name, ' ', $helpOption, '\'.', PHP_EOL;
        }
    }
}
