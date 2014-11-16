<?php
namespace Hyperframework\Cli;

use Hyperframework;
use Hyperframework\EnvironmentBuilder;
use Hyperframework\Config;
use Hyperframework\ConfigFileLoader;
use Hyperframework\Cli\CommandParser;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        static::dispatch();
    }

    public static function dispatch() {
        $rootNamespace = Hyperframework\APP_ROOT_NAMESPACE;
        $isCollection =
            Config::get('hyperframework.cli.command_collection.enable') == true;
        $configFileName = 'command.php';
        if ($isCollection) {
            $configFileName = 'command_collection.php';
        }
        ConfigParser::run(
            ConfigFileLoader::loadPhp($configFileName),
            $isCollection
        );
        if ($rootNamespace !== null) {
            $rootNamespace .= '\\';
        }
        if ($isCollection) {
            $class = $rootNamespace . 'CommandCollection';
            $commandCollection = new $class;
            $commandCollection->execute(array());
            $class = $rootNamespace . 'Commands\HelloCommand';
            $command = new $class;
            $command->execute(array());
        } else {
            $class = $rootNamespace . 'Command';
            $command = new $class;
            $command->execute(array());
        }
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ErrorHandler::run();
    }
}
