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
        if (isset($elements['arguments'])) {
            $this->setArguments($elements['arguments']);
        }
        if ($this->hasOption('help')) {
            $this->renderHelp();
            $this->quit();
        }
        if ($this->hasOption('version')) {
            $this->renderVersion();
            $this->quit();
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

    public function getCommandConfig() {
        if ($this->commandConfig === null) {
            $hasMultipleCommand = $this->hasMultipleCommands()
            $class = (string)Config::get(
                'hyperframework.cli.command_config_class'
            );
            if ($class === '') {
                $this->commandConfig = new CommandConfig($hasMultipleCommand);
            } else {
                $this->commandConfig = new $class($hasMultipleCommand);
            }
        }
        return $this->commandConfig;
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
        $config = $this->getCommandConfig();
        $name = $config->getName();
        $options = null;
        if ($exception instanceof SubcomandParsingException) {
            $subcommand = $exception->getSubcommand();
            $name .= ' ' . $subcommand;
            $options = $config->getOptions($subcommand);
        } else {
            $options = $config->getOptions();
        }
        if (isset($options['help'])) {
            echo 'See \'', $name, ' --help\'.', PHP_EOL;
            $helpOption = '--help';
        }
    }

    protected function hasMultipleCommands() {
        //'multiple_commands';
        return false;
    }

    protected function finalize() {}
}
