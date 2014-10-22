<?php
namespace Hyperframework\Cli;

use Hyperframework\EnvironmentBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        static::runApp();
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
//        ConfigParser::run();
//        $isCollection =
//            Config::get('hyperframework.cli.command_collection.enable') === true;
//        //$configPath = $isCollection ? 'command_collection.php' : 'command.php';
//        if ($isCollection) {
//            $commandParser = new CommandParser;
//            $collectionClass = ConfigParser::getCollectionClass();
//            $collection = new $collectionClass;
//            $collection->execute(CommandParser::getCollectionOptions());
//        }
//        $commandName = CommandParser::getCommandName();
//        CollectionParser::parse();
//        if ($hasCommandOptions) {
//            array_unshift($commandOptions, $commandArgs);
//        }
//        $command = new $commandClass;
//        call_user_method_array($command, 'execute', $commandArgs);


    }
}
