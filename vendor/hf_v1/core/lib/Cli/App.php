<?php
namespace Hyperframework\Cli;

use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    private $commandName = null;
    private $collectionOptions = array();
    private $commandOptions = array();
    private $arguments = array();
    private $hasCollection;

    public function run() {
        $this->initialize();
        if ($this->hasCollection) {
            $this->executeCollection();
        }
        $this->executeCommand();
        $this->finalize();
    }

    protected function executeCollection() {
    }

    protected function executeCommand() {
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    public function getCollectionOptions() {
        return $this->collectionOptions;
    }

    public function getCommandOptions() {
        return $this->commandOptions;
    }

    public function getArguments() {
        return $this->arguments;
    }

    public function getCommandName() {
        return $this->commandName;
    }

    protected function initialize() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $this->hasCollection =
            Config::get('hyperframework.cli.command_collection.enable') === true;
        $commandParser = new CommandParser;
        $commandParser->parse($hasCollection);
        $configPath = $isCollection ? 'command_collection.php' : 'command.php';
        if ($isCollection) {
            $collectionConfig =
                ConfigFileLoader::loadPhp('command_collection.php');
        }
        //parse command
    }

    protected function finalize() {
    }
}
