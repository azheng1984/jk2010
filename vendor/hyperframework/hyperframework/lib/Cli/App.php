<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;
use Hyperframework\Common\App as Base;

class App extends Base implements IApp {
    private $commandConfig;
    private $appRootPath;
    private $options = [];
    private $arguments = [];

    /**
     * @param string $appRootPath
     */
    public static function run($appRootPath) {
        $app = static::createApp($appRootPath);
        $app->executeCommand();
        $app->finalize();
    }

    /**
     * @param string $appRootPath
     */
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

    /**
     * @return string[]
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getOption($name) {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    /**
     * @return string[]
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * @return ICommandConfig
     */
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

    /**
     * @param string $appRootPath
     * @return static
     */
    protected static function createApp($appRootPath) {
        return new static($appRootPath);
    }

    /**
     * @param string[] $options
     */
    protected function setOptions(array $options) {
        $this->options = $options;
    }

    /**
     * @param string[] $arguments
     */
    protected function setArguments(array $arguments) {
        $this->arguments = $arguments;
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

    /**
     * @param CommandParsingException $exception
     */
    protected function renderCommandParsingError(
        CommandParsingException $exception
    ) {
        echo $exception->getMessage(), PHP_EOL;
        $config = $this->getCommandConfig();
        $name = $config->getName();
        $subcommandName = $exception->getSubcommandName();
        if ($subcommandName !== null) {
            $name .= ' ' . $subcommandName;
        }
        if ($config->getOptionConfig('help', $subcommandName) !== null) {
            echo 'See \'', $name, ' --help\'.', PHP_EOL;
        }
    }
}
