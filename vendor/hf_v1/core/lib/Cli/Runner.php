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
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::build($rootNamespace, $rootPath);
        ErrorHandler::run();
    }
}
