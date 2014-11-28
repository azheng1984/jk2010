<?php
namespace Hyperframework\Cli;

class MultipleCommandApp extends App {
    private $subcommand;
    private $globalOptions;

    public function hasMultipleCommand() {
        return true;
    }

    protected function fetchCommandElements($elements) {
        if (isset($elements['global_options'])) {
            $this->setGlobalOptions($elements['global_options']);
        } else {
            $this->setGlobalOptions([]);
        }
        if (isset($elements['subcommand'])) {
            $this->setSubcommand($elements['subcommand']);
        } else {
            $this->setSubcommand(null);
        }
        parent::fetchCommandElements($elements);
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

    protected function executeCommand() {
        if ($this->hasGlobalOption('version')) {
            $this->renderVersion();
            return;
        }
        if ($this->hasGlobalOption('help') || $this->hasOption('help')) {
            $this->renderHelp();
            return;
        }
        if ($this->isSubcommand() === false) {
            $this->executeGlobalCommand();
            return;
        }
        $config = $this->getCommandConfig();
        $class = $config->get('class', $this->getSubcommand());
        $subcommand = new $class($this);
        $arguments = $this->getArguments();
        call_user_method_array('execute', $subcommand, $arguments);
    }

    protected function setSubcommand($value) {
        $this->subcommand = $value;
    }

    protected function setGlobalOptions(array $globalOptions) {
        $this->globalOptions = $globalOptions;
    }

    protected function executeGlobalCommand() {
        $this->renderHelp();
    }
}
