<?php
namespace Hyperframework\Cli;

use Hyperframework;
use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    private $commandParser;

    public function run() {
        ArgumentConfigParser::_test();
        OptionConfigParser::_test();
        CommandParser::_test();
        $this->initialize();
        $this->executeCommand();
        $this->finalize();
    }

    protected function hasOption() {
    }

    protected function executeCommand() {
        if ($this->hasOption('--help')) {
            //render HelpView
        }
        if ($this->hasOption('--version')) {
            //render version
        }
        $class = Hyperframework\APP_ROOT_NAMESPACE . '\Command';
        $command = new $class($class);
        call_user_method_array(
            'execute', $command, $this->commandParser->getArguments()
        );
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $this->commandParser = new CommandParser;
        $this->commandParser->parse($this->hasMultipleCommand());
        var_dump($this->commandParser->getSubcommand());
        var_dump($this->commandParser->getParentOptions());
        var_dump($this->commandParser->getOptions());
        var_dump($this->commandParser->getArguments());
        //$configPath = $isCollection ? 'command_collection.php' : 'command.php';
        //$collectionConfig = ConfigFileLoader::loadPhp($configPath);
        //parse command
    }

    protected function hasMultipleCommand() {
        return false;
    }

    protected function finalize() {}
}
