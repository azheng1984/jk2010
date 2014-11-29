<?php
namespace Hyperframework\Cli;

class MultipleCommandApp extends App {
    private $subcommand;
    private $globalOptions;

    public function hasMultipleCommands() {
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

    public function hasSubcommand() {
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

    protected function setSubcommand($value) {
        $this->subcommand = $value;
    }

    protected function setGlobalOptions(array $globalOptions) {
        $this->globalOptions = $globalOptions;
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
        if ($this->hasSubcommand()) {
            $this->executeSuncommand();
            return;
        }
        $this->executeGlobalCommand();
    }

    protected function executeSubcommand() {
        $config = $this->getCommandConfig();
        $subcommandClass = $config->get('class', $this->getSubcommand());
        if ($subcommandClass === null) {
            throw new Exception;
        }
        $subcommand = new $subcommandClass($this);
        $arguments = $this->getArguments();
        call_user_method_array('execute', $subcommand, $arguments);
    }

    protected function executeGlobalCommand() {
        $this->renderHelp();
    }
}
