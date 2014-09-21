<?php
namespace Hyperframework\Cli;

use Hyperframework\EnvironmentBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        static::dispatch();
    }

    public static function dispatch() {
        $rootNamespace = \Hyperframework\APP_ROOT_NAMESPACE;
        $mode = 'complex';
        if ($mode === 'simple') {
            $class = $rootNamespace . '\Command';
            $command = new $class;
            $command->execute(array());
        } else {
            $class = $rootNamespace . '\CommandCollection';
            $commandCollection = new $class;
            $commandCollection->execute(array());
            $class = $rootNamespace . '\Commands\HelloCommand';
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
