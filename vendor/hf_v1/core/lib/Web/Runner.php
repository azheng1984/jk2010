<?php
namespace Hyperframework\Web;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\Config;

class Runner {
    public static function run($appRootNamespace, $appRootPath) {
        static::initialize($appRootNamespace, $appRootPath);
        static::runApp();
    }

    protected static function initialize($appRootNamespace, $appRootPath) {
        chdir($rootPath);
        require dirname(__DIR__) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::build($appRootNamespace, $appRootPath);
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }
}
