<?php
namespace Hyperframework\Cli;

use Exception;

class App {
    private $commandConfig;
    private $options = [];
    private $arguments = [];

    public function run() {
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    public function getArguments() {
        return $this->arguments;
    }

    protected function setArguments(array $arguments) {
        $this->arguments = $arguments;
    }

    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    public function getOption($name) {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    public function getOptions() {
        return $this->options;
    }

    protected function setOptions(array $options) {
        $this->options = $options;
    }

    public function getCommandConfig() {
        if ($this->commandConfig === null) {
            $this->commandConfig = new CommandConfig;
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
        $command = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$command, 'execute'], $arguments);
    }

    protected function renderHelp($commandParsingException = null) {
        $view = new HelpView($this, $commandParsingException);
        $view->render();
    }

    protected function renderVersion() {
        $config = $this->getCommandConfig();
        $version = $config->get('version');
        if ($version == '' && (string)$version === '') {
            echo 'undefined', PHP_EOL;
            return;
        }
        echo $version, PHP_EOL;
    }

    protected function initialize() {
        $elements = $this->parseCommand();
        if (isset($elements['options'])) {
            $this->setOptions($elements['options']);
        }
        if ($this->hasOption('help')) {
            $this->renderHelp();
            $this->quit();
        }
        if ($this->hasOption('version')) {
            $this->renderVersion();
            $this->quit();
        }
        if (isset($elements['arguments'])) {
            $this->setArguments($elements['arguments']);
        }
    }

    protected function parseCommand() {
        try {
            $commandConfig = $this->getCommandConfig();
            return CommandParser::parse($commandConfig);
        } catch (CommandParsingException $e) {
            $this->renderHelper($e);
            $this->quit();
        }
    }

    protected function finalize() {}
}
