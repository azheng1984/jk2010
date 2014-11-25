<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;

class Runner {
    private static $appRootPath;

    public static function run() {
        static::initialize();
        static::runApp();
    }

    protected static function initialize() {
        static::initializeConfig();
        static::initializeErrorHandler();
    }

    protected static function initializeConfig() {
        Config::set('hyperframework.app_root_path', static::getAppRootPath());
        Config::import('init.php');
    }

    protected static function runApp() {
        throw new Exception;
    }

    protected static function initializeErrorHandler() {
        throw new Exception;
    }

    protected static function setAppRootPath($value) {
        self::$appRootPath = $value;
    }

    protected static function getAppRootPath() {
        if (self::$appRootPath === null) {
            throw new Exception;
        }
        return self::$appRootPath;
    }
}
