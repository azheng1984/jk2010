<?php
namespace Hyperframework\Cli;

use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    public function run() {
        $args = $_SERVER['argv'];
        array_shift($args);
        $isCollection =
            Config::get('hyperframework.cli.command_collection.enable') === true;
        $configPath = $isCollection ? 'command_collection.php' : 'command.php';
        if ($isCollection) {
            $collectionConfig =
                ConfigFileLoader::loadPhp('command_collection.php');
        }
        //execute collection
    }

    public function executeCollection() {
    }

    public function quit() {
        $this->finalize();
        exit;
    }

    protected function initialize() {
    }

    protected function finalize() {
    }
}
