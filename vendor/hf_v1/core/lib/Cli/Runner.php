<?php
namespace Hyperframework\Cli;

use Hyperframework\EnvironmentBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        static::runApp();
    }

    public static function runApp() {
        $app = new App;
        $app->run();
//        $rootNamespace = Hyperframework\APP_ROOT_NAMESPACE;
//        $isCollection =
//            Config::get('hyperframework.cli.enable_subcommands') == true;
//        $configFileName = 'command.php';
//        if ($isCollection) {
//            $configFileName = 'command_collection.php';
//        }
//        ConfigParser::run(
//            ConfigFileLoader::loadPhp($configFileName),
//            $isCollection
//        );
//        if ($rootNamespace !== null) {
//            $rootNamespace .= '\\';
//        }
//        if ($isCollection) {
//            $class = $rootNamespace . 'CommandCollection';
//            $commandCollection = new $class;
//            $commandCollection->execute(array());
//            $class = $rootNamespace . 'Commands\HelloCommand';
//            $command = new $class;
//            $command->execute(array());
//        } else {
//            $class = $rootNamespace . 'Command';
//            $command = new $class;
//            $command->execute(array());
//        }
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::build($rootNamespace, $rootPath);
        ErrorHandler::run();
    }
}
