<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Common\ConfigException;

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
            $class = Config::getString(
                'hyperframework.cli.command_config_class', ''
            );
            if ($class === '') {
                $this->commandConfig = new CommandConfig;
            } else {
                if (class_exists($class) === false) {
                    throw new ConfigException(
                        "Command config class '$class' does not exist."
                    );
                }
                $this->commandConfig = new $class;
            }
        }
        return $this->commandConfig;
    }

    public function quit() {
        $this->finalize();
        exit();
    }

    protected function executeCommand() {
        $class = $this->getCommandConfig()->getClass();
        if (class_exists($class) === false) {
            throw new ConfigException("Command class '$class' does not exist.");
        }
        $command = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$command, 'execute'], $arguments);
    }

    protected function renderHelp() {
        $class = Config::getString('hyperframework.cli.help_class', '');
        if ($class === '') {
            $class = 'Hyperframework\Cli\Help';
        } else {
            if (class_exists($class) === false) {
                throw new ConfigException(
                    "Help class '$class' does not exist."
                );
            }
        }
        $help = new $class($this);
        $help->render();
    }

    protected function renderVersion() {
        $commandConfig = $this->getCommandConfig();
        $version = (string)$commandConfig->getVersion();
        if ($version === '') {
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
        $subcommand = null;
        if ($exception instanceof SubcommandParsingException) {
            $subcommand = $exception->getSubcommand();
            $name .= ' ' . $subcommand;
        }
        $options = $config->getOptions($subcommand);
        if (isset($options['help'])) {
            echo 'See \'', $name, ' --help\'.', PHP_EOL;
            $helpOption = '--help';
        }
    }

    protected function finalize() {}
}
