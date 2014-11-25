<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;

class Runner {
    public static function run() {
        static::initialize();
        static::runApp();
    }

    protected static function initialize() {
        static::initializeAppRootPath();
        static::initializeConfig();
        static::initializeErrorHandler();
    }

    protected static function runApp() {
        throw new Exception;
    }

    protected static function initializeConfig() {
        Config::import('init.php');
    }

    protected static function initializeAppRootPath() {
        throw new Exception;
    }

    protected static function initializeErrorHandler() {
        throw new Exception;
    }
}
