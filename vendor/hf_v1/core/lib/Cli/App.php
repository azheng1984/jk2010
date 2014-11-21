<?php
namespace Hyperframework\Cli;

use Hyperframework;
use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    private $commandParser;
    private $commandClass;
    private $options;
    private $arguments;

    public function run() {
        ArgumentConfigParser::_test();
        OptionConfigParser::_test();
        CommandParser::_test();
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    protected function hasOption($name) {
        $options = $this->commandParser->getOptions();
        return isset($options[$name]);
    }

    protected function executeCommand() {
        if ($this->hasOption('--version')) {
            //check has version config
            $this->renderVersion();
            return;
        }
        $class = $this->getCommandClass();
        $command = new $class($this);
        if ($this->hasOption('--help')) {
            $command->renderHelp();
        } else {
            call_user_func_array(
                [$command, 'execute'], $this->commandParser->getArguments()
            );
        }
    }

    protected function getCommandClass() {
        //read config file to get class name
        return Hyperframework\APP_ROOT_NAMESPACE . '\Command';
    }

    protected function renderVersion() {
        //render version by config
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $this->commandParser = new CommandParser;
        $this->commandParser->parse($this->hasMultipleCommands());
        var_dump($this->commandParser->getSubcommand());
        var_dump($this->commandParser->getGlobalOptions());
        var_dump($this->commandParser->getOptions());
        var_dump($this->commandParser->getArguments());
        //$configPath = $isCollection ? 'command_collection.php' : 'command.php';
        //$collectionConfig = ConfigFileLoader::loadPhp($configPath);
        //parse command
    }

    protected function hasMultipleCommands() {
        return false;
    }

    protected function finalize() {}
}
