<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\App as Base;

class App extends Base {
    private $commandConfig;
    private $appRootPath;
    private $options = [];
    private $arguments = [];

    public static function run($appRootPath) {
        $app = static::createApp($appRootPath);
        $app->executeCommand();
        $app->finalize();
    }

    public function __construct($appRootPath) {
        parent::__construct($appRootPath);
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

    protected static function createApp($appRootPath) {
        return new static($appRootPath);
    }

    protected function setOptions(array $options) {
        $this->options = $options;
    }

    public function getCommandConfig() {
        if ($this->commandConfig === null) {
            $configName = 'hyperframework.cli.command_config_class';
            $class = Config::getString($configName, '');
            if ($class === '') {
                $this->commandConfig = new CommandConfig;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist, set using config "
                            . "'$configName'."
                    );
                }
                $this->commandConfig = new $class;
            }
        }
        return $this->commandConfig;
    }

    protected function executeCommand() {
        $commandConfig = $this->getCommandConfig();
        $class = $commandConfig->getClass();
        if (class_exists($class) === false) {
            throw new ClassNotFoundException(
                "Command class '$class' does not exist."
            );
        }
        $command = new $class($this);
        $arguments = $this->getArguments();
        call_user_func_array([$command, 'execute'], $arguments);
    }

    protected function renderHelp() {
        $configName = 'hyperframework.cli.help_class';
        $class = Config::getString($configName, '');
        if ($class === '') {
            $help = new Help($this);
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$class' does not exist, set using config"
                        . " '$configName'."
                );
            }
            $help = new $class($this);
        }
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
        $subcommand = null;
        if ($exception instanceof SubcommandParsingException) {
            $subcommand = $exception->getSubcommand();
            $name .= ' ' . $subcommand;
        }
        $options = $config->getOptions($subcommand);
        if (isset($options['help'])) {
            echo 'See \'', $name, ' --help\'.', PHP_EOL;
        }
    }
}
