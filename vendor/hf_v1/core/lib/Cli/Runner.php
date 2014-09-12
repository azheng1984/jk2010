<?php
namespace Hyperframework\Cli;

use Hyperframework\Config;
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

//        $app = new App;
//        $app->run();
    }
}
