<?php
namespace Hyperframework\Cli;

use Exception;
use Hyperframework\Common\Config;

class App {
    private $commandConfig;
    private $options = [];
    private $arguments = [];

    public function __construct() {
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

    public function run() {
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

    public function getCommandConfig($name = null) {
        if ($this->commandConfig === null) {
            $this->commandConfig = new CommandConfig;
        }
        if ($name === null) {
            return $this->commandConfig;
        }
        return $this->commandConfig->get($name);
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function executeCommand() {
        $class = $this->getCommandConfig('class');
        if ($class === null) {
            throw new Exception;
        }
        $command = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$command, 'execute'], $arguments);
    }

    protected function renderHelp() {
        $class = (string)$this->getCommandConfig('help_class');
        if ($class === '') {
            $class = (string)Config::get('hyperframework.cli.help_class');
            if ($class === '') {
                $class = 'Hyperframework\Cli\Help';
            }
        }
        $help = new $class($this);
        $help->render();
    }

    protected function renderVersion() {
        $version = $this->getCommandConfig('version');
        if ($version == '' && (string)$version === '') {
            echo 'undefined', PHP_EOL;
            return;
        }
        echo $version, PHP_EOL;
    }

    protected function parseCommand() {
        try {
            $commandConfig = $this->getCommandConfig();
            return CommandParser::parse($commandConfig);
        } catch (CommandParsingException $e) {
            $this->renderCommandParsingError($e);
            $this->quit();
        }
    }

    protected function renderCommandParsingError($exception) {
        echo $exception->getMessage(), PHP_EOL;
        $name = (string)$this->getCommandConfig('name');
        if ($name === '') {
            throw new Exception;
        }
        $options = $this->getCommandConfig('options');
        $helpOption = null;
        if (isset($options['help'])) {
            $helpOption = '--help';
        } elseif (isset($options['h'])) {
            $helpOption = '-h';
        }
        if ($helpOption !== null) {
            echo 'See \'', $name, ' ', $helpOption, '\'.', PHP_EOL;
        }
    }

    protected function finalize() {}
}
