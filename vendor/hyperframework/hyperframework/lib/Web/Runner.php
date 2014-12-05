<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\Runner as Base;

class Runner extends Base {
    public static function run() {
        static::initialize();
        static::runApp();
    }

    protected static function initializeAppRootPath() {
        Config::set('hyperframework.app_root_path', dirname(getcwd()));
    }

    protected static function initializeErrorHandler() {
        ErrorHandler::run();
    }
}
