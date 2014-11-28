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
    private $commandParsingException;

    public function run() {
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    public function hasMultipleCommands() {
        return false;
    }

    public function getCommandParsingException() {
        return $this->commandParsingException;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function hasOption($name) {
        $options = $this->getOptions();
        return isset($options[$name]);
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
        $class = $config->get('class');
        if ($class === null) {
            throw new Exception;
        }
        if ($this->hasOption('version')) {
            $this->renderVersion();
            return;
        }
        if ($this->hasOption('help')) {
            $this->renderHelp();
            return;
        }
        $command = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$command, 'execute'], $arguments);
    }

    protected function renderHelp() {
        $view = new HelpView($this);
        $view->render();
    }

    protected function renderVersion() {
        $config = $this->getCommandConfig();
        print_r($config->getAll());
        $version = $config->get('version');
        if ($version == '' && '' === (string)$version) {
            echo 'undefined', PHP_EOL;
            return;
        }
        echo 'version ', $version, PHP_EOL;
    }

    protected function initialize() {
        try {
            $elements = $this->parseCommand();
            $this->fetchCommandElements($elements);
        } catch (CommandParsingException $e) {
            $this->setCommandParsingException($e);
            $this->renderHelper();
            $this->quit();
        }
    }

    protected function parseCommand() {
        return CommandParser::parse($this->getCommandConfig());
    }

    protected function fetchCommandElements($elements) {
        if (isset($elements['options'])) {
            $this->setOptions($elements['options']);
        } else {
            $this->setOptions([]);
        }
        if (isset($elements['arguments'])) {
            $this->setArguments($elements['arguments']);
        } else {
            $this->setArguments([]);
        }
    }

    protected function setArguments(array $arguments) {
        $this->arguments = $arguments;
    }

    protected function setOptions(array $options) {
        $this->options = $options;
    }

    protected function setCommandParsingException($value) {
        $this->commandParsingException = $value;
    }

    protected function finalize() {}
}
