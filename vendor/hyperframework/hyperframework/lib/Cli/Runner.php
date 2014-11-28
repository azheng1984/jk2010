<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;
use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    private static $appRootPath;

    public static function run($appRootPath) {
        self::$appRootPath = $appRootPath;
        static::initialize();
        static::runApp();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function initializeAppRootPath() {
        Config::set('hyperframework.app_root_path', self::$appRootPath);
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
