<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\ConfigException;

class MultipleCommandApp extends App {
    private $commandConfig;
    private $subcommand;
    private $globalOptions = [];

    public function __construct() {
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

    protected function setSubcommand($value) {
        $this->subcommand = $value;
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
        $subcommandClass = $config->getClass($this->getSubcommand());
        if (class_exists($subcommandClass) === false) {
            throw new ConfigException(
                "Subcommand config error. Class '$subcommandClass' 不存在"
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
