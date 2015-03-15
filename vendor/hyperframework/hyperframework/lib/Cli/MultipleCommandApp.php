<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\App as CommonApp;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class MultipleCommandApp extends App {
    private $commandConfig;
    private $subcommand;
    private $globalOptions = [];

    public function __construct($appRootPath) {
        CommonApp::__construct($appRootPath);
        Config::set('hyperframework.cli.enable_subcommand', true);
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

    public function hasSubcommand() {
        return $this->subcommand !== null;
    }

    public function getSubcommand() {
        return $this->subcommand;
    }

    protected function setSubcommand($subcommand) {
        $this->subcommand = $subcommand;
    }

    protected function executeCommand() {
        if ($this->hasSubcommand()) {
            $this->executeSubcommand();
            return;
        }
        $this->executeGlobalCommand();
    }

    protected function executeSubcommand() {
        $config = $this->getCommandConfig();
        $subcommand = $this->getSubcommand();
        $subcommandClass = $config->getClass($subcommand);
        if (class_exists($subcommandClass) === false) {
            throw new ClassNotFoundException(
                "Command class '$subcommandClass' does not exist."
            );
        }
        $subcommand = new $subcommandClass($this);
        $arguments = $this->getArguments();
        call_user_func_array([$subcommand, 'execute'], $arguments);
    }

    protected function executeGlobalCommand() {
        $this->renderHelp();
    }
}
