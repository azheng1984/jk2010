<?php
namespace Hyperframework\Cli;

class MultipleCommandApp extends App {
    private $subcommand;
    private $globalOptions;

    public function hasMultipleCommand() {
        return true;
    }

    protected function fetchCommandElements($elements) {
        $this->globalOptions = $result['global_options'];
        if (isset($elements['subcommand'])) {
            $this->subcommand = $result['subcommand'];
            parent::fetchCommandElements($elements);
        }
    }

    public function getSubcommand() {
        return $this->subcommand;
    }

    public function isSubcommand() {
        return $this->subcommand !== null;
    }

    public function getGlobalOptions() {
        return $this->globalOptions;
    }

    public function getGlobalOption($name) {
        $globalOptions = $this->getGlobalOptions();
        if (isset($globalOptions[$name])) {
            return $globalOptions[$name];
        }
    }

    public function hasGlobalOption($name) {
        $globalOptions = $this->getGlobalOptions();
        return isset($globalOptions[$name]);
    }

    public function getArguments() {
        if ($this->isSubcommand()) {
            return parent::getArguments();
        }
        throw new Exception;
    }

    public function getOptions() {
        if ($this->isSubcommand()) {
            return parent::getOptions();
        }
        throw new Exception;
    }

    protected function executeCommand() {
        try {
            if ($this->hasGlobalOption('version')) {
                $this->renderVersion();
                return;
            }
            if ($this->hasGlobalOption('help')) {
                $this->renderGlobalHelp();
                return;
            }
            if ($this->isSubcommand() === false) {
                $this->executeGlobalCommand();
            }
            $config = $this->getCommandConfig();
            $class = $config->get('class', $this->getSubcommand());
            $command = new $class($this);
            if ($this->hasOption('help')) {
                $command->renderHelp();
            } else {
                if ($this->)
                call_user_method_array(
                    'execute', $command, $this->commandParser->getArguments()
                );
            }
        }
    }

    protected function renderGlobalHelp() {
        $view = new HelpView($this->getApp());
        $view->render();
    }

    protected function executeGlobalCommand() {
        $this->renderGlobalHelp();
    }
}
