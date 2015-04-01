<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\App as CommonApp;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class MultipleCommandApp extends App {
    private $commandConfig;
    private $subcommandName;
    private $globalOptions = [];

    public function __construct($appRootPath) {
        CommonApp::__construct($appRootPath);
        Config::set('hyperframework.cli.enable_subcommand', true);
        $elements = $this->parseCommand();
        if (isset($elements['global_options'])) {
            $this->setGlobalOptions($elements['global_options']);
        }
        if (isset($elements['subcommand_name'])) {
            $this->setSubcommandName($elements['subcommand_name']);
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

    public function hasSubcommand() {
        return $this->subcommandName !== null;
    }

    public function getSubcommandName() {
        return $this->subcommandName;
    }

    protected function setGlobalOptions($globalOptions) {
        $this->globalOptions = $globalOptions;
    }

    protected function setSubcommandName($subcommandName) {
        $this->subcommandName = $subcommandName;
    }

    protected function executeCommand() {
        if ($this->hasSubcommand()) {
            $this->executeSubcommand();
        } else {
            $this->executeGlobalCommand();
        }
    }

    protected function executeSubcommand() {
        $commandConfig = $this->getCommandConfig();
        $name = $this->getSubcommandName();
        $class = $commandConfig->getClass($name);
        if (class_exists($class) === false) {
            throw new ClassNotFoundException(
                "Subcommand class '$class' does not exist."
            );
        }
        $subcommand = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$subcommand, 'execute'], $arguments);
    }

    protected function executeGlobalCommand() {
        $this->renderHelp();
    }
}
