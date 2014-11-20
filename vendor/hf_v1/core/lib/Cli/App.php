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
        ArgumentConfigParser::_test();
        OptionConfigParser::_test();
        CommandParser::_test();
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
            Config::get('hyperframework.cli.enable_subcommands') === true;
        $commandParser = new CommandParser;
        $commandParser->parse($this->hasCollection);
        var_dump($commandParser->getSubcommand());
        var_dump($commandParser->getCollectionOptions());
        var_dump($commandParser->getOptions());
        var_dump($commandParser->getArguments());
        //$configPath = $isCollection ? 'command_collection.php' : 'command.php';
        //$collectionConfig = ConfigFileLoader::loadPhp($configPath);
        //parse command
    }

    protected function finalize() {}
}
