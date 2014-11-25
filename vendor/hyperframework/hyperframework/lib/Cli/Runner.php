<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    private $appRootPath;

    public static function run($appRootPath) {
        self::$appRootPath = $appRootPath;
        parent::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }

    protected static function getAppRootPath() {
        if (self::$appRootPath === null) {
            throw new Exception;
        }
        return self::$appRootPath;
    }
}
