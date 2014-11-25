<?php
namespace Hyperframework\Cli;

class MultipleCommandApp extends App {
    private $subcommand;
    private $globalOptions;

    public function hasMultipleCommand() {
        return true;
    }

    protected function fetchCommandElements($elements) {
        $this->subcommand = $result['subcommand'];
        $this->globalOptions = $result['global_options'];
    }

    public function getSubcommand() {
        if ($this->isSubcommand() === false) {
            return;
        }
    }

    public function isSubcommand() {
    }

    public function getGlobalOption() {
    }

    protected function executeCommand() {
        $config = $this->getCommandConfig();
        if ($this->hasGlobalOption('version')) {
            $this->renderVersion();
            return;
        }
        if ($this->hasGlobalOption('help')) {
            $this->renderGlobalHelp();
        }
        $class = $config->get('class');
        $command = new $class($this);
        if ($this->hasOption('help')) {
            $command->renderHelp();
        } else {
            call_user_method_array(
                'execute', $command, $this->commandParser->getArguments()
            );
        }
    }

    protected function renderGlobalHelp() {
        //render default global help
    }
}
