<?php
namespace Hyperframework\Web;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Config;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        static::runApp();
    }

    protected static function initialize($rootNamespace, $rootPath) {
        chdir($rootPath);
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::build($rootNamespace, $rootPath);
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
