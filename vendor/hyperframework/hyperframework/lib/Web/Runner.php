<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    private $appRootPath;

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }

    protected static function runApp() {
        $app = new App;
        $app->run();
    }

    protected static function getAppRootPath() {
        if (self::$appRootPath === null) {
            self::$appRootPath = dirname(getcwd());
        }
        return self::$appRootPath;
    }
}
