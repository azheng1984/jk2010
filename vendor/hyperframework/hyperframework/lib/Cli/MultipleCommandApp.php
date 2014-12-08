<?php
namespace Hyperframework\Cli;

use Exception;

class MultipleCommandApp extends App {
    private $commandConfig;
    private $subcommand;
    private $globalOptions = [];

    public function __construct() {
        $elements = $this->parseCommand();
        if (isset($elements['global_options'])) {
            $this->setGlobalOptions($elements['global_options']);
        }
        if (isset($elements['subcommand'])) {
            $this->setSubcommand($elements['subcommand']);
            if (isset($elements['options'])) {
                $this->setOptions($elements['options']);
            }
            if (isset($elements['arguments'])) {
                $this->setArguments($elements['arguments']);
            }
        }
        if ($this->hasGlobalOption('help') || $this->hasOption('help')) {
            $this->renderHelp();
            $this->quit();
        }
        if ($this->hasGlobalOption('version')) {
            $this->renderVersion();
            $this->quit();
        }
    }

    public function getGlobalOptions() {
        return $this->globalOptions;
    }

    public function getGlobalOption($name) {
        if (isset($this->globalOptions[$name])) {
            return $this->globalOptions[$name];
        }
    }

    public function hasGlobalOption($name) {
        return isset($this->globalOptions[$name]);
    }

    protected function setGlobalOptions(array $globalOptions) {
        $this->globalOptions = $globalOptions;
    }

    protected function getSubcommand() {
        return $this->subcommand;
    }

    protected function setSubcommand($value) {
        $this->subcommand = $value;
    }

    protected function hasSubcommand() {
        return $this->subcommand !== null;
    }

    public function getCommandConfig($name = null) {
        if ($this->commandConfig === null) {
            $this->commandConfig = new CommandConfig(true);
        }
        if ($name === null) {
            return $this->commandConfig;
        }
        return $this->commandConfig->get($name, $this->getSubcommand());
    }

    protected function executeCommand() {
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
        call_user_func_array([$subcommand, 'execute'], $arguments);
    }

    protected function executeGlobalCommand() {
        $this->renderHelp();
    }
}
