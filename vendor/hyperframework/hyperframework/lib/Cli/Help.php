<?php
namespace Hyperframework\Cli;

class Help {
    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function render() {
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
}
