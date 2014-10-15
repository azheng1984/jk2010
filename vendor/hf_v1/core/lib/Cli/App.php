<?php
namespace Hyperframework\Cli;

use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class App {
    public function run() {
        $isCollection =
            Config::get('hyperframework.cli.command_collection.enable') === true;
        $configPath = $isCollection ? 'command_collection.php' : 'command.php';
        if ($isCollection) {
            $collectionConfig =
                ConfigFileLoader::loadPhp('command_collection.php');
        }
    }
}
