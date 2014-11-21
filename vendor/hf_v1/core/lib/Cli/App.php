<?php
namespace Hyperframework\Cli;

use Hyperframework;
use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    private $commandConfig;
    private $options;
    private $arguments;

    public function run() {
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    public function getOption($name) {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    public function getArguments() {
        return $this->arguments;
    }

    protected function executeCommand() {
        $config = $this->getCommandConfig();
        if ($this->hasOption('version')) {
            $this->renderVersion();
            return;
        }
        $class = $config->get('class');
        if ($class === null) {
            throw new Exception;
        }
        $command = new $class($this);
        if ($this->hasOption('help')) {
            $command->renderHelp();
        } else {
            call_user_func_array(
                [$command, 'execute'], $this->getArguments()
            );
        }
    }

    protected function renderVersion() {
        $config = $this->getCommandConfig();
        $version = $config->get('version');
        if ($version == '' && '' === (string)$version) {
            $version = 'unknown';
        }
        echo 'version ', $version;
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $result = CommandParser::parse($this->getCommandConfig());
        $this->fetchCommandElements($result);
    }

    protected function fetchCommandElements($elements) {
        $this->options = $elements['options'];
        $this->arguments = $elements['arguments'];
    }

    public function getCommandConfig() {
        if ($this->commandConfig === null) {
            $this->commandConfig = new CommandConfig(
                $this->hasMultipleCommands()
            );
        }
        return $this->commandConfig;
    }

    protected function hasMultipleCommands() {
        return false;
    }

    protected function finalize() {}
}
