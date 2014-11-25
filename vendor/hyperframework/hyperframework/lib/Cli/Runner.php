<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    public static function run($appRootPath) {
        static::setAppRootPath($appRootPath);
    }

    public static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
