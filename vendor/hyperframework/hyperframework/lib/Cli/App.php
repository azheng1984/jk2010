<?php
namespace Hyperframework\Cli;

use Exception;
use Hyperframework;
use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigFileLoader;

class App {
    private $commandConfig;
    private $options;
    private $arguments;

    public function run() {
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    public function hasMultipleCommands() {
        return false;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function hasOption($name) {
        $options = $this->getOptions();
        isset($options[$name]);
    }

    public function getOption($name) {
        $options = $this->getOptions();
        if (isset($options[$name])) {
            return $this->options[$name];
        }
    }

    public function getOptions() {
        return $this->options;
    }

    public function getCommandConfig() {
        if ($this->commandConfig === null) {
            $this->commandConfig = new CommandConfig(
                $this->hasMultipleCommands()
            );
        }
        return $this->commandConfig;
    }

    public function quit() {
        $this->finalize();
        exit;
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

    protected function initialize() {
        $result = CommandParser::parse($this->getCommandConfig());
        $this->fetchCommandElements($result);
    }

    protected function fetchCommandElements($elements) {
        $this->options = $elements['options'];
        $this->arguments = $elements['arguments'];
    }

    protected function finalize() {}
}
