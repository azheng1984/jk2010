<?php
namespace Hyperframework\Cli;

class Help {
    private $app;
    private $errorMessage;

    public function __construct($app, $errorMessage = null) {
        $this->app = $app;
        $this->errorMessage = $errorMessage;
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

    protected function renderCompressedOptions() {
    }

    protected function renderOptions() {
        echo 'Options:' . PHP_EOL;
    }

    protected function renderErrorHelp() {
        echo $this->errorMessage . PHP_EOL;
        $this->renderUsage();
        //short version for command parsing exception
    }
}
