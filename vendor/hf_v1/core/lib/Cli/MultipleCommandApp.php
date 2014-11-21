<?php
namespace Hyperframework\Cli;

class MultipleCommandApp extends App {
    private $subcommand;
    private $globalOptions;

    public function getSubcommand() {
        if ($this->isSubcommand() === false) {
            return;
        }
    }

    public function hasMultipleCommands() {
        return true;
    }

    public function isSubcommand() {
    }

    public function getGlobalOption() {
    }

    protected function executeCommand() {
        if ($this->hasGlobalOption('version')) {
            $this->renderVersion();
            return;
        }
        if ($this->hasGlobalOption('help')) {
            $this->renderGlobalHelp();
        }
        $class = $this->getCommandClass();
        $command = new $class($this);
        if ($this->hasOption('help')) {
            $command->renderHelp();
        } else {
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }

    protected function getCommandClass() {
        //read config file to get class name
        if ($this->isSubcommand()) {
            $tmp = ucwords(str_replace('-', ' ', $this->getSubcommand()));
            $tmp = str_replace(' ', '', $tmp) . 'Command';
            return Hyperframework\APP_ROOT_NAMESPACE . '\\' . $tmp;
        }
        return parent::getCommandClass();
    }

    protected function renderGlobalHelp() {
        //render default global help
    }
}
